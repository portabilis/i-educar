<?php

namespace iEducar\Modules\AcademicYear\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class DisciplineNotLinkedToRegistrationException extends Exception
{

    public function __construct($schoolId, $disciplineId, $academicYearId, $levelId)
    {
        parent::__construct(
             "O componente curricular". $disciplineId ." não está vinculado ao ano letivo ". $academicYearId ." da série ". $levelId ." da escola ". $schoolId, Error::DISCIPLINE_NOT_ENROLLED_IN_SCHOOL_LEVELS
        );

        $this->enrollmentId = $enrollmentId;
        $this->schoolId = $schoolId;
        $this->disciplineId = $disciplineId;
        $this->academicYearId = $academicYearId;
        $this->levelId = $levelId;
    }

    /**
     * Return more information about error.
     *
     * @return array
     */
    public function getExtraInfo()
    {
        return [
            'enrollment_code' => $this->enrollmentId,
            'school_code' => $this->schoolId,
            'discipline_code' => $this->disciplineId,
            'academicYear_code' => $this->academicYearId,
            'level_code' => $this->levelId,
        ];

    }
}
