<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\LegacyUser;
use App\Models\PerformanceEvaluation;
use Tests\EloquentTestCase;

class PerformanceEvaluationTest extends EloquentTestCase
{
    public $relations = [
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'employee' => Employee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return PerformanceEvaluation::class;
    }
}
