<?php

namespace Tests\Unit\Eloquent;

use App\Models\Course;
use Tests\EloquentTestCase;

class CourseTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Course::class;
    }
}
