<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeAllocation;
use App\Models\LegacyPeriod;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class EmployeeAllocationTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'period' => LegacyPeriod::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeAllocation::class;
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->period->nome, $this->model->periodName);
    }
}
