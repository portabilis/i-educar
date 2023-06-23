<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeGraduation;
use App\Models\EmployeeGraduationDiscipline;
use Tests\EloquentTestCase;

class EmployeeGraduationDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'employeeGraduations' => EmployeeGraduation::class,
    ];

    protected function getEloquentModelName(): string
    {
        return EmployeeGraduationDiscipline::class;
    }
}
