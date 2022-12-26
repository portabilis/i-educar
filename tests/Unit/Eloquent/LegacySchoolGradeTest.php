<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacySchoolGrade;
use Tests\EloquentTestCase;

class LegacySchoolGradeTest extends EloquentTestCase
{
    protected $relations = [
        'grade' => LegacyGrade::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolGrade::class;
    }
}
