<?php

use App\Models\EducacensoDegree;
use App\Models\EducacensoInstitution;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeeGraduation::class, relations: [
    'employee' => Employee::class,
    'educacensoDegree' => EducacensoDegree::class,
    'educacensoInstitution' => EducacensoInstitution::class,
]));

EloquentModelTestCase::registerModelTests();

test('scope professor', function () {
    $employeeGraduation = $this->createNewModel();

    $employeeQuery = EmployeeGraduation::query()
        ->ofEmployee($employeeGraduation->employee->id)
        ->first();

    $this->assertInstanceOf(EmployeeGraduation::class, $employeeQuery);
    $this->assertInstanceOf(Employee::class, $employeeQuery->employee);
    $this->assertEquals($employeeGraduation->id, $employeeQuery->id);
});
