<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClassGrade;
use Tests\EloquentTestCase;

class LegacySchoolClassGradeTest extends EloquentTestCase
{
    protected $relations = [
        'grade' => LegacyGrade::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClassGrade::class;
    }
}
