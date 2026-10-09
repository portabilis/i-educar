<?php

use App\Models\EducacensoInstitution;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use App\Models\LegacySchool;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EducacensoInstitution::class, relations: [
    'schools' => LegacySchool::class,
    'employeeGraduations' => EmployeeGraduation::class,
    'employees' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();
