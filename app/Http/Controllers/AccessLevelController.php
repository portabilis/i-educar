<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Models\LegacyUserType;
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
     * @param Request        $request
     * @param LegacyUserType $userType
     *
     * @throws Throwable
     */
    private function store(Request $request, LegacyUserType $userType)
    {
        $userType->fill([
            'nm_tipo' => $request->input('name'),
            'nivel' => $request->input('level'),
            'descricao' => $request->input('description'),
            'ref_funcionario_cad' => $request->user()->getKey(),
            'data_cadastro' => now(),
        ]);

        $processes = collect($request->input('processes'))->mapWithKeys(function ($level, $process) {
            return [
                $process => [
                    'visualiza' => intval($level >= LegacyUserType::CAN_VIEW),
                    'cadastra' => intval($level >= LegacyUserType::CAN_MODIFY),
                    'exclui' => intval($level >= LegacyUserType::CAN_REMOVE),
                ]
            ];
        });

        $userType->saveOrFail();

        $userType->menus()->syncWithoutDetaching($processes);
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function new(Request $request)
    {
        $view = $this->show($request, new LegacyUserType());

        $this->breadcrumb('Cadastrar tipo de usuário');

        return $view;
    }

    /**
     * @param Request        $request
     * @param LegacyUserType $userType
     *
     * @return View
     */
    public function show(Request $request, LegacyUserType $userType)
    {
        $this->menu(554)->breadcrumb('Editar tipo de usuário');

        $processes = $userType->load('menus')->getProcesses()->toArray();

        /** @var User $user */
        $user = $request->user();

        $userProcesses = $user->type->getProcesses();

        $menus = Menu::user($user)->map(function (Menu $menu) use ($userProcesses) {
            return new Collection([
                'menu' => $menu,
                'processes' => $menu->processes($menu->title, $userProcesses),
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
     * @param Request        $request
     * @param LegacyUserType $userType
     *
     * @return RedirectResponse
     */
    public function create(Request $request, LegacyUserType $userType)
    {
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
     * @param Request        $request
     * @param LegacyUserType $userType
     *
     * @return RedirectResponse
     */
    public function update(Request $request, LegacyUserType $userType)
    {
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
     * @param LegacyUserType $userType
     * @param Connection $connection
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete(LegacyUserType $userType, Connection $connection)
    {
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

        return redirect()
            ->route('usertype.index')
            ->with('success', 'Registro excluído com sucesso.');
    }
}
