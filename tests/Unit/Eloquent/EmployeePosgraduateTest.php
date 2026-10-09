<?php

use App\Models\Employee;
use App\Models\EmployeePosgraduate;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeePosgraduate::class, relations: [
    'employee' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();

test('scope professor', function () {
    $employeePosGraduation = $this->createNewModel();

    $employeeQuery = EmployeePosgraduate::query()
        ->ofEmployee($employeePosGraduation->employee->id)
        ->first();

    $this->assertInstanceOf(EmployeePosgraduate::class, $employeeQuery);
    $this->assertInstanceOf(Employee::class, $employeeQuery->employee);
    $this->assertEquals($employeePosGraduation->id, $employeeQuery->id);
});
