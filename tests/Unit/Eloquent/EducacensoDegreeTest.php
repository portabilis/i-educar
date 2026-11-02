<?php

use App\Models\EducacensoDegree;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EducacensoDegree::class, relations: [
    'employeeGraduations' => EmployeeGraduation::class,
    'employees' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();

test('constants', function () {
    $this->assertEquals(1, EducacensoDegree::GRAU_TECNOLOGICO);
    $this->assertEquals(2, EducacensoDegree::GRAU_LICENCIATURA);
    $this->assertEquals(3, EducacensoDegree::GRAU_BACHARELADO);
    $this->assertEquals(4, EducacensoDegree::GRAU_SEQUENCIAL);
});
