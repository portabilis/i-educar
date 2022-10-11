<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeInep;
use Tests\EloquentTestCase;

class EmployeeInepTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeInep::class;
    }
}
