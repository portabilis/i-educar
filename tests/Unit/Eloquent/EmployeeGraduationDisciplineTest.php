<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeGraduationDiscipline;
use Tests\EloquentTestCase;

class EmployeeGraduationDisciplineTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeGraduationDiscipline::class;
    }
}
