<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'course' => LegacyCourse::class,
        'grade' => LegacyLevel::class,
        'school' => LegacySchool::class,
        'enrollments' => Collection::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClass::class;
    }
}
