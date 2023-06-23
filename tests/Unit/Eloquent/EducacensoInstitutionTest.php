<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoInstitution;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use App\Models\LegacySchool;
use Tests\EloquentTestCase;

class EducacensoInstitutionTest extends EloquentTestCase
{
    protected $relations = [
        'schools' => LegacySchool::class,
        'employeeGraduations' => EmployeeGraduation::class,
        'employees' => Employee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducacensoInstitution::class;
    }
}
