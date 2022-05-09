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
        $posgraduate = EmployeePosgraduate::ofEmployee($employee)->get();
        foreach ($posgraduate as $pos) {
            $pos->delete();
        }
    }

    /**
     * @param EmployeePosgraduateValueObject $valueObject
     */
    public function storePosgraduate(EmployeePosgraduateValueObject $valueObject)
    {
        $employeePosgraduate = new EmployeePosgraduate();
        $employeePosgraduate->employee_id = $valueObject->employeeId;
        $employeePosgraduate->entity_id = $valueObject->entityId;
        $employeePosgraduate->type_id = $valueObject->typeId;
        $employeePosgraduate->area_id = $valueObject->areaId;
        $employeePosgraduate->completion_year = $valueObject->completionYear;
        $employeePosgraduate->save();
    }
}
