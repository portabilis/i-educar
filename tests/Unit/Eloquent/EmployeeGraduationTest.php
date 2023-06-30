<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoDegree;
use App\Models\EducacensoInstitution;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use Tests\EloquentTestCase;

class EmployeeGraduationTest extends EloquentTestCase
{
    public $relations = [
        'employee' => Employee::class,
        'educacensoDegree' => EducacensoDegree::class,
        'educacensoInstitution' => EducacensoInstitution::class,
    ];

    protected function getEloquentModelName(): string
    {
        return EmployeeGraduation::class;
    }

    /** @test  */
    public function scopeProfessor(): void
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
