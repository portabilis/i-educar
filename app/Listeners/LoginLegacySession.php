<?php

namespace App\Listeners;

use App\Facades\Asset;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Services\UrlPresigner;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class LoginLegacySession
{
    /**
     * @return int
     */
    private function getStudentsCount()
    {
        return LegacyStudent::query()->count();
    }

    /**
     * @return int
     */
    private function getTeachersCount()
    {
        return DB::table('pmieducar.servidor')
            ->join('pmieducar.servidor_funcao', 'servidor_funcao.ref_cod_servidor', '=', 'servidor.cod_servidor')
            ->join('pmieducar.funcao', 'funcao.cod_funcao', '=', 'servidor_funcao.ref_cod_funcao')
            ->where('funcao.professor', 1)
            ->count();
    }

    /**
     * @return int
     */
    private function getClassesCount()
    {
        return LegacySchoolClass::query()->count();
    }

    /**
     * @param User $user
     *
     * @return object
     */
    private function getLoggedUserInfo($user)
    {
        $institution = app(LegacyInstitution::class);
        $individual = new LegacyIndividual(['idpes' => $user->cod_usuario]);

        $picture = $individual->picture()->first()
            ? (new UrlPresigner())->getPresignedUrl($individual->picture()->first()->caminho)
            : Asset::get('intranet/imagens/user-perfil.png');

        try {
            $createdAt = Carbon::create($user->created_at)->getTimestamp();
        } catch (Throwable $throwable) {
            $createdAt = Carbon::now()->subYear()->getTimestamp();
        }

        return (object) [
            'personId' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $createdAt,
            'institution' => $institution->name,
            'city' => $institution->city,
            'state' => $institution->state,
            'students_count' => $this->getStudentsCount(),
            'teachers_count' => $this->getTeachersCount(),
            'classes_count' => $this->getClassesCount(),
            'picture' => $picture
        ];
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $loggedUser = $this->getLoggedUserInfo($event->user);
        Session::put([
            'itj_controle' => 'logado',
            'id_pessoa' => $event->user->id,
            'pessoa_setor' => $event->user->employee->department_id,
            'tipo_menu' => $event->user->employee->menu_type,
            'nivel' => $event->user->type->level,
            'logged_user' => $loggedUser,
            'logged_user_picture' => $loggedUser->picture
        ]);
    }
}
