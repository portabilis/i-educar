<?php

namespace App\Http\Controllers;

class LegacyFakeAuthController
{
    /**
     * Do a fake login when running functional tests.
     *
     * @return void
     */
    public function doFakeLogin()
    {
        if (empty($_SESSION)) {
            session_start();
        }

        $_SESSION['itj_controle'] = 'logado';
        $_SESSION['id_pessoa'] = '1';
        $_SESSION['pessoa_setor'] = null;
        $_SESSION['menu_opt'] = false;
        $_SESSION['tipo_menu'] = '1';
        $_SESSION['nivel'] = '1';

        session_write_close();
    }

    /**
     * Do a fake logout when running functional tests.
     *
     * @return void
     */
    public function doFakeLogout()
    {
        if (empty($_SESSION)) {
            session_start();
        }

        $_SESSION = [];

        session_destroy();
    }
}
