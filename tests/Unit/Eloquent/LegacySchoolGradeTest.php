<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolGrade;
use Tests\EloquentTestCase;

class LegacySchoolGradeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolGrade::class;
    }
}
