<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeGraduation;
use Tests\EloquentTestCase;

class EmployeeGraduationTest extends EloquentTestCase
{
    public $relations = [
        'employee' => Employee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeGraduation::class;
    }

    /** @test  */
    public function scopeProfessor()
    {
        $employeeGraduation = $this->createNewModel();

        $employeeQuery = EmployeeGraduation::query()
            ->ofEmployee($employeeGraduation->employee->id)
            ->first();

        $this->assertInstanceOf(EmployeeGraduation::class, $employeeQuery);
        $this->assertInstanceOf(Employee::class, $employeeQuery->employee);
        $this->assertEquals($employeeGraduation->id, $employeeQuery->id);
    }
}
