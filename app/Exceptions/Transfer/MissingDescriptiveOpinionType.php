<?php

namespace App\Exceptions\Transfer;

class MissingDescriptiveOpinionType extends TransferException
{
    public function __construct()
    {
        $message = 'O tipo de parecer da regra de avaliação é inválido';

        parent::__construct($message);
    }
}
