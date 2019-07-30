<?php

namespace App\Exceptions\Unification;

use RuntimeException;

class WithoutPermission extends RuntimeException
{
    public function __construct()
    {
        $message = 'Você não tem permissão para desfazer essa unificação.';

        parent::__construct($message);
    }
}