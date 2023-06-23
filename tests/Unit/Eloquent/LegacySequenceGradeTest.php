<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacySequenceGrade;
use Tests\EloquentTestCase;

class LegacySequenceGradeTest extends EloquentTestCase
{
    protected $relations = [
        'gradeOrigin' => LegacyGrade::class,
        'gradeDestiny' => LegacyGrade::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySequenceGrade::class;
    }
}
