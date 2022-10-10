<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeGraduation;
use Tests\EloquentTestCase;

class EmployeeGraduationTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeGraduation::class;
    }
}
