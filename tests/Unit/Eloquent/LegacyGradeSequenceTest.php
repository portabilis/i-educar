<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacyGradeSequence;
use Tests\EloquentTestCase;

class LegacyGradeSequenceTest extends EloquentTestCase
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
        return LegacyGradeSequence::class;
    }
}
