<?php

namespace App\Exceptions\Educacenso;

use RuntimeException;

class NotImplementedYear extends RuntimeException
{
    public function __construct($year)
    {
        $message = sprintf('A importação do ano %s não foi implementada', $year);

        parent::__construct($message);
    }
}
