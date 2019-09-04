<?php

namespace App\Exceptions\Console;

use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use DomainException;

class MissingSchoolGradeException extends DomainException
{
    public function __construct(LegacySchool $school, LegacyLevel $grade)
    {
        $message = 'Não existe registro em escola série para escola %s e série %s';

        $message = sprintf(
            $message, $school->getKey(), $grade->getKey()
        );

        parent::__construct($message);
    }
}
