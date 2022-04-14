<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use Tests\EloquentTestCase;

class LegacyCourseTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourse::class;
    }
}
