<?php

namespace App\Exceptions\Unification;

use App\Models\Individual;
use App\Models\Student;
use RuntimeException;

class InactiveMainUnification extends RuntimeException
{
    public function __construct($unification)
    {
        if ($unification->type == Student::class) {
            $message = 'O aluno está inativo ou foi unificado com outro aluno.';
        }

        if ($unification->type == Individual::class) {
            $message = 'A pessoa está inativa ou foi unificada com outra pessoa.';
        }

        parent::__construct($message);
    }
}
