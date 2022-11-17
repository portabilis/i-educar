<?php

namespace App\Exceptions\Console;

use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use DomainException;

class MissingSchoolGradeException extends DomainException
{
    public function __construct(LegacySchool $school, LegacyGrade $grade)
    {
        $message = 'Não existe registro em escola série para escola %s e série %s';

        $message = sprintf(
            $message,
            $school->getKey(),
            $grade->getKey()
        );

        parent::__construct($message);
    }
}
