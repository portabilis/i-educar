<?php

use App\Models\EmployeeGraduation;
use App\Models\EmployeeGraduationDiscipline;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeeGraduationDiscipline::class, relations: [
    'employeeGraduations' => EmployeeGraduation::class,
]));

EloquentModelTestCase::registerModelTests();
