<?php

namespace iEducar\Modules\Auth;

class GetSessionData
{
    /**
     * @return array
     */
    public static function get()
    {
        @session_start();

        return $_SESSION;
    }
}
