<?php

namespace App\Exceptions\Educacenso;

use RuntimeException;

class InvalidSchoolInep extends RuntimeException implements ImportException
{
    public function __construct(int $inep, string $schoolName)
    {
        parent::__construct("Não foi possível encontrar a escola {$schoolName} com o INEP {$inep}");
    }
}
