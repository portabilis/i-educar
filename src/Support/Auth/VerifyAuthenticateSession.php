<?php

namespace iEducar\Modules\Auth;

class VerifyAuthenticateSession
{
    /**
     * @return bool
     */
    public static function isAuthenticated()
    {
        @session_start();

        if (!isset($_SESSION['itj_controle'])) {
            return false;
        }

        if ('logado' == $_SESSION['itj_controle']) {
            return true;
        }

        return false;
    }
}
