<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Session;

class LoginLegacySession
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Session::put([
            'itj_controle' => 'logado',
            'id_pessoa' => $event->user->id,
            'pessoa_setor' => $event->user->employee->department_id,
            'tipo_menu' => $event->user->employee->menu_type,
            'nivel' => $event->user->type->level,
        ]);
    }
}
