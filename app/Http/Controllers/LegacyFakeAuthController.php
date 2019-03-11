<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LegacyFakeAuthController
{
    /**
     * Do a fake login when running functional tests.
     *
     * @return void
     */
    public function doFakeLogin()
    {
        Session::put([
            'itj_controle' => 'logado',
            'id_pessoa' => '1',
            'pessoa_setor' => null,
            'tipo_menu' => '1',
            'nivel' => '1',
        ]);
    }

    /**
     * Do a fake logout when running functional tests.
     *
     * @return void
     */
    public function doFakeLogout()
    {
        Session::flush();
    }
}
