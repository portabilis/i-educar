<?php

namespace App\Exceptions\Console;

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use DomainException;

class MissingSchoolCourseException extends DomainException
{
    public function __construct(LegacySchool $school, LegacyCourse $course)
    {
        $message = 'Não existe registro em escola curso para escola %s e curso %s';

        $message = sprintf(
            $message,
            $school->getKey(),
            $course->getKey()
        );

        parent::__construct($message);
    }
}
