<?php

namespace App\Http\Controllers;

use App\Models\LegacyInstitution;
use App\Services\MenuCacheService;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function user(Request $request)
    {
        $user = $request->user()->load('type');

        return [
            'cod_usuario' => $user->cod_usuario,
            'ativo' => $user->ativo,
            'ref_cod_tipo_usuario' => $user->ref_cod_tipo_usuario,
            'person' => [
                'id' => $user->person->id,
                'name' => $user->person->name,
                'nome' => $user->person->nome,
                'url' => $user->person->url,
                'email' => $user->person->email,
                'slug' => $user->person->slug,
            ],
            'type' => [
                'cod_tipo_usuario' => $user->type->cod_tipo_usuario,
                'nm_tipo' => $user->type->nm_tipo,
                'descricao' => $user->type->descricao,
                'nivel' => $user->type->nivel,
                'ativo' => $user->type->ativo,
                'updated_at' => $user->type->updated_at,
            ],
            'employee' => [
                'ref_cod_pessoa_fj' => $user->employee->ref_cod_pessoa_fj,
                'matricula' => $user->employee->matricula,
                'ativo' => $user->employee->ativo,
                'email' => $user->employee->email,
                'matricula_interna' => $user->employee->matricula_interna,
            ],
        ];
    }

    public function authorization(Request $request)
    {
        return $request->user()->processes->map(function ($process) {
            $ability = $process->process;

            $data = [];

            if ($process->pivot->visualiza) {
                $data[] = "view:$ability";
            }

            if ($process->pivot->cadastra) {
                $data[] = "modify:$ability";
            }

            if ($process->pivot->exclui) {
                $data[] = "remove:$ability";
            }

            return $data;
        })->flatten();
    }

    public function menus(MenuCacheService $menus, Request $request)
    {
        return $menus->getMenuByUser($request->user());
    }

    public function config(Repository $config)
    {
        return [
            'footer' => $config->get('legacy.config.ieducar_internal_footer'),
        ];
    }

    public function institution()
    {
        $institution = app(LegacyInstitution::class);

        return [
            'name' => $institution->nm_instituicao,
            'logo' => config('legacy.report.logo_file_name'),
            'city' => $institution->cidade . '/' . $institution->ref_sigla_uf,
        ];
    }

    public function fallback($uri)
    {
        if (str_starts_with($uri, 'web')) {
            return redirect('intranet/educar_index.php');
        }

        return abort(404);
    }

    public function home()
    {
        return redirect(config('app.home'));
    }
}
