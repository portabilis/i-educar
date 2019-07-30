<?php

namespace App\Exceptions\Unification;

use RuntimeException;

class UndoInactiveUnification extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('A unificação já foi desfeita anteriormente.');
    }
}