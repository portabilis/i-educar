<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacySchoolClass;
use RuntimeException;

class NoVacancyException extends RuntimeException
{
    /**
     * Não há vagas na turma.
     *
     * @param LegacySchoolClass $schoolClass
     */
    public function __construct(LegacySchoolClass $schoolClass)
    {
        $message = 'Não há vagas na turma "%s" #%s.';

        $message = sprintf(
            $message, $schoolClass->name, $schoolClass->id
        );

        parent::__construct($message);
    }
}
