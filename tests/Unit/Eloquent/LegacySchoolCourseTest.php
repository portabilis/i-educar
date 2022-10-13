<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use Tests\EloquentTestCase;

class LegacySchoolCourseTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
        'course' => LegacyCourse::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacySchoolCourse::class;
    }
}
