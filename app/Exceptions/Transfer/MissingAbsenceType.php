<?php

namespace App\Exceptions\Transfer;

class MissingAbsenceType extends TransferException
{
    public function __construct()
    {
        $message = 'O tipo de presença da regra de avaliação é inválido';

        parent::__construct($message);
    }
}
