<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeePosgraduate;
use Tests\EloquentTestCase;

class EmployeePosgraduateTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'employee' => Employee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeePosgraduate::class;
    }

    public function testScopeProfessor()
    {
        $employeePosGraduation = $this->createNewModel();

        $employeeQuery = EmployeePosgraduate::query()
            ->ofEmployee($employeePosGraduation->employee->id)
            ->first();

        $this->assertInstanceOf(EmployeePosgraduate::class, $employeeQuery);
        $this->assertInstanceOf(Employee::class, $employeeQuery->employee);
        $this->assertEquals($employeePosGraduation->id, $employeeQuery->id);
    }
}
