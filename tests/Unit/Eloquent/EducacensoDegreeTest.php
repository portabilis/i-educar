<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoDegree;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use Tests\EloquentTestCase;

class EducacensoDegreeTest extends EloquentTestCase
{
    protected $relations = [
        'employeeGraduations' => EmployeeGraduation::class,
        'employees' => Employee::class,
    ];

    protected function getEloquentModelName(): string
    {
        return EducacensoDegree::class;
    }

    public function testConstants(): void
    {
        $this->assertEquals(1, EducacensoDegree::GRAU_TECNOLOGICO);
        $this->assertEquals(2, EducacensoDegree::GRAU_LICENCIATURA);
        $this->assertEquals(3, EducacensoDegree::GRAU_BACHARELADO);
        $this->assertEquals(4, EducacensoDegree::GRAU_SEQUENCIAL);
    }
}
