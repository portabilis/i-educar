<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use Tests\EloquentTestCase;

class LegacySchoolClassGradeTest extends EloquentTestCase
{
    protected $relations = [
        'grade' => LegacyGrade::class,
        'schoolClass' => LegacySchoolClass::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacySchoolClassGrade::class;
    }
}
