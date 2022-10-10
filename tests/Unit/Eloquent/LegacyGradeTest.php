<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use Tests\EloquentTestCase;

class LegacyGradeTest extends EloquentTestCase
{
    protected $relations = [
        'course' => LegacyCourse::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGrade::class;
    }
}
