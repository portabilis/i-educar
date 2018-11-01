<?php

namespace iEducar\Modules\Auth;

class GetSessionData
{
    public static function get()
    {
        @session_start();

        return $_SESSION;
    }
}