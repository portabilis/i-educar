<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeAllocation;
use App\Models\LegacySchool;
use Database\Factories\EmployeeAllocationFactory;
use Tests\EloquentTestCase;

class EmployeeAllocationTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeAllocation::class;
    }
}
