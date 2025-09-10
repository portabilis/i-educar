<?php

namespace App\Traits;

use App\Models\LegacyUserType;
use Illuminate\Support\Facades\DB;

trait HasNotificationUsers
{
    /**
     * Busca usuários que devem receber notificações baseado no processo e escola
     *
     * @return array
     */
    public function getUsers(int $process, int $school)
    {
        return DB::table('pmieducar.usuario as u')
            ->select('u.cod_usuario')
            ->distinct()
            ->join('pmieducar.tipo_usuario as tu', 'tu.cod_tipo_usuario', 'u.ref_cod_tipo_usuario')
            ->leftJoin('pmieducar.escola_usuario as eu', 'eu.ref_cod_usuario', 'u.cod_usuario')
            // menus somente para quem não é Admin
            ->leftJoin('pmieducar.menu_tipo_usuario as mtu', function ($j) {
                $j->on('mtu.ref_cod_tipo_usuario', 'u.ref_cod_tipo_usuario');
                $j->where('mtu.visualiza', 1);
            })
            ->leftJoin('public.menus as m', 'm.id', 'mtu.menu_id')
            ->where('u.ativo', 1)
            // condição de escola ou nível institucional
            ->where(function ($q) use ($school) {
                $q->where('eu.ref_cod_escola', $school);
                $q->orWhere('tu.nivel', '<=', LegacyUserType::LEVEL_INSTITUTIONAL);
            })
            // condição de menu: ou é Admin, ou tem menu válido no processo
            ->where(function ($q) use ($process) {
                $q->where('tu.nivel', LegacyUserType::LEVEL_ADMIN);
                $q->orWhere('m.process', $process);
            })
            ->get()
            ->toArray();
    }
}
