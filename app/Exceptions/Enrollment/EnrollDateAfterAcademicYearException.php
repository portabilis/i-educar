<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacySchoolClass;
use DateTime;
use RangeException;

class EnrollDateAfterAcademicYearException extends RangeException
{
    public function __construct(LegacySchoolClass $schoolClass, DateTime $date)
    {
        $message = 'A data de enturmação %s é posterior ao fim do ano acadêmico %s.';

        $message = sprintf(
            $message,
            $date->format('d/m/Y'),
            $schoolClass->end_academic_year->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
