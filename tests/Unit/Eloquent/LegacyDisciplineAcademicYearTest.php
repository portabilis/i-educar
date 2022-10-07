<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyGrade;
use Tests\EloquentTestCase;

class LegacyDisciplineAcademicYearTest extends EloquentTestCase
{
    protected $relations = [
        'discipline' => LegacyDiscipline::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineAcademicYear::class;
    }
}
