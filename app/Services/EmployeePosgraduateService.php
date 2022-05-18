<?php

namespace App\Services;

use App\Models\EmployeePosgraduate;
use iEducar\Modules\ValueObjects\EmployeePosgraduateValueObject;

class EmployeePosgraduateService
{
    /**
     * @param $employee
     */
    public function deleteAll($employee)
    {
        EmployeePosgraduate::ofEmployee($employee)->delete();
    }

    /**
     * @param EmployeePosgraduateValueObject $valueObject
     */
    public function storePosgraduate(EmployeePosgraduateValueObject $valueObject)
    {
        EmployeePosgraduate::create([
            'employee_id' => $valueObject->employeeId,
            'entity_id' => $valueObject->entityId,
            'type_id' => $valueObject->typeId,
            'area_id' => $valueObject->areaId,
            'completion_year' => $valueObject->completionYear,
        ]);
    }
}
