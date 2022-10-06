<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineAcademicYear;
use Tests\EloquentTestCase;

class LegacyDisciplineAcademicYearTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineAcademicYear::class;
    }
}
