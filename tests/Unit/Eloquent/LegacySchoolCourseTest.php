<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolCourse;
use Tests\EloquentTestCase;

class LegacySchoolCourseTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        $this->markTestSkipped();
        return LegacySchoolCourse::class;
    }
}
