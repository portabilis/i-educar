<?php

namespace App\Services;

use App\Models\EmployeeGraduation;
use iEducar\Modules\ValueObjects\EmployeeGraduationValueObject;

class EmployeeGraduationService
{
    /**
     * @return EmployeeGraduation[]
     */
    public function getEmployeeGraduations($employee)
    {
        return EmployeeGraduation::ofEmployee($employee)->get();
    }

    public function deleteAll($employee)
    {
        $graduations = EmployeeGraduation::ofEmployee($employee)->get();
        foreach ($graduations as $graduation) {
            $graduation->delete();
        }
    }

    public function storeGraduation(EmployeeGraduationValueObject $valueObject)
    {
        $employeeGraduation = new EmployeeGraduation();
        $employeeGraduation->employee_id = $valueObject->employeeId;
        $employeeGraduation->course_id = $valueObject->courseId;
        $employeeGraduation->completion_year = $valueObject->completionYear;
        $employeeGraduation->college_id = $valueObject->collegeId;
        $employeeGraduation->save();
    }
}
