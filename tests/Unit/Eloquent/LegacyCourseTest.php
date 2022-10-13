<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use Tests\EloquentTestCase;

class LegacyCourseTest extends EloquentTestCase
{
    protected $relations = [
        'grades' => [LegacyGrade::class],
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourse::class;
    }
}
