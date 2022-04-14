<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolAcademicYear;
use Tests\EloquentTestCase;

class LegacySchoolAcademicYearTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolAcademicYear::class;
    }
}
