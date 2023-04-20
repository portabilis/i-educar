<?php

namespace App\Exceptions\Educacenso;

use RuntimeException;

class InvalidFileDate extends RuntimeException implements ImportException
{
    public function __construct()
    {
        parent::__construct('Ocorreu um erro na validação do ano do arquivo importado!');
    }
}
