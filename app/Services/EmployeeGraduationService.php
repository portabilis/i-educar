<?php

namespace App\Services;

use App\Models\EmployeeGraduation;
use iEducar\Modules\ValueObjects\EmployeeGraduationValueObject;

class EmployeeGraduationService
{
    /**
     * @param $employee
     * @return EmployeeGraduation[]
     */
    public function getEmployeeGraduations($employee)
    {
        return EmployeeGraduation::ofEmployee($employee)->get();
    }

    /**
     * @param $employee
     */
    public function deleteAll($employee)
    {
        $graduations = EmployeeGraduation::ofEmployee($employee)->get();
        foreach ($graduations as $graduation) {
            $graduation->delete();
        }
    }

    /**
     * @param EmployeeGraduationValueObject $valueObject
     */
    public function storeGraduation(EmployeeGraduationValueObject $valueObject)
    {
        $employeeGraduation = new EmployeeGraduation();
        $employeeGraduation->employee_id = $valueObject->employeeId;
        $employeeGraduation->course_id = $valueObject->courseId;
        $employeeGraduation->completion_year = $valueObject->completionYear;
        $employeeGraduation->college_id = $valueObject->collegeId;
        $employeeGraduation->discipline_id = $valueObject->disciplineId;
        $employeeGraduation->save();
    }
}
