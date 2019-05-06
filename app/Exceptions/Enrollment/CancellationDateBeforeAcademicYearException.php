<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacySchoolClass;
use DateTime;
use RangeException;

class CancellationDateBeforeAcademicYearException extends RangeException
{
    public function __construct(LegacySchoolClass $schoolClass, DateTime $date)
    {
        $message = 'A data de saída %s é anterior ao início do ano acadêmico %s.';

        $message = sprintf(
            $message, $date->format('d/m/Y'), $schoolClass->begin_academic_year->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
