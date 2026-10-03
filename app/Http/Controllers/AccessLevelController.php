<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Models\LegacyUserType;
use App\Services\MenuCacheService;
use App\User;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

class AccessLevelController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->breadcrumb('Tipos de usuário', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
            url('intranet/educar_tipo_usuario_lst.php') => 'Tipos de Usuário',
        ]);
    }

    /**
     * @throws Throwable
     */
    private function store(Request $request, LegacyUserType $userType)
    {
        /** @var User $user */
        $user = $request->user();

        $userType->fill([
            'nm_tipo' => $request->input('name'),
            'nivel' => $request->input('level'),
            'descricao' => $request->input('description'),
            'ref_funcionario_cad' => $request->user()->getKey(),
            'data_cadastro' => now(),
            'updated_at' => now(),
        ]);

        $userProcesses = $user->type->getProcesses();

        $processes = collect($request->input('processes'))->mapWithKeys(function ($level, $process) use ($user, $userProcesses) {
            if (!$user->isAdmin()) {
                $level = min((int) $level, (int) ($userProcesses[$process] ?? 0));
            }

            return [
                $process => [
                    'visualiza' => intval($level >= LegacyUserType::CAN_VIEW),
                    'cadastra' => intval($level >= LegacyUserType::CAN_MODIFY),
                    'exclui' => intval($level >= LegacyUserType::CAN_REMOVE),
                ],
            ];
        });

        $userType->saveOrFail();

        $userType->menus()->syncWithoutDetaching($processes);

        app(MenuCacheService::class)->flushMenuTag($userType->cod_tipo_usuario);
    }

    /**
     * Impede que o usuário crie ou altere tipos de usuário com nível de acesso
     * acima do seu. Quanto menor o número do nível, maior o privilégio.
     */
    private function authorizeUserTypeLevel(User $user, int $level): void
    {
        if ($user->isAdmin()) {
            return;
        }

        abort_if(
            $level < $user->getLevel(),
            403,
            'Não é permitido gerenciar tipos de usuário com nível de acesso superior ao seu.'
        );
    }

    /**
     * @return View
     */
    public function new(Request $request)
    {
        $view = $this->show($request, new LegacyUserType);

        $this->breadcrumb('Cadastrar tipo de usuário');

        return $view;
    }

    /**
     * @return View
     */
    public function show(Request $request, LegacyUserType $userType)
    {
        $this->menu(554)->breadcrumb('Editar tipo de usuário');

        $processes = $userType->load('menus')->getProcesses()->toArray();

        /** @var User $user */
        $user = $request->user();

        $userProcesses = $user->type->getProcesses();

        $menus = Menu::user($user)->map(function (Menu $menu) use ($userProcesses, $userType) {
            return new Collection([
                'menu' => $menu,
                'processes' => $menu->processes($menu->title, $userProcesses, $userType->nivel),
            ]);
        });

        foreach ($userProcesses as $process => $status) {
            if (isset($processes[$process])) {
                continue;
            }

            $processes[$process] = 0;
        }

        return view('accesslevel.index', [
            'menus' => $menus,
            'userType' => $userType,
            'processes' => $processes,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function create(Request $request, LegacyUserType $userType)
    {
        $this->authorizeUserTypeLevel($request->user(), (int) $request->input('level'));

        try {
            $this->store($request, $userType);
        } catch (Throwable $throwable) {
            return redirect()
                ->route('usertype.new')
                ->with('error', 'Não foi possível cadastrar o registro.');
        }

        return redirect()
            ->route('usertype.index')
            ->with('success', 'Registro cadastrado com sucesso.');
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request, LegacyUserType $userType)
    {
        $this->authorizeUserTypeLevel($request->user(), (int) $userType->nivel);
        $this->authorizeUserTypeLevel($request->user(), (int) $request->input('level'));

        try {
            $this->store($request, $userType);
        } catch (Throwable $throwable) {
            return redirect()
                ->route('usertype.new')
                ->with('error', 'Não foi possível editar o registro.');
        }

        return redirect()
            ->route('usertype.index')
            ->with('success', 'Registro editado com sucesso.');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete(Request $request, LegacyUserType $userType, Connection $connection)
    {
        $this->authorizeUserTypeLevel($request->user(), (int) $userType->nivel);

        try {
            $connection->beginTransaction();
            $userType->menus()->detach();
            $userType->delete();
            $connection->commit();
        } catch (Throwable $throwable) {
            $connection->rollBack();

            return redirect()
                ->route('usertype.show', ['userType' => $userType])
                ->with('error', 'Não foi possível excluir o registro.');
        }

        app(MenuCacheService::class)->flushMenuTag($userType->cod_tipo_usuario);

        return redirect()
            ->route('usertype.index')
            ->with('success', 'Registro excluído com sucesso.');
    }
}
