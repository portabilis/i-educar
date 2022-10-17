<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyStudentAbsence;
use Tests\EloquentTestCase;

class LegacyDisciplineAbsenceTest extends EloquentTestCase
{
    protected $relations = [
        'studentAbsence' => LegacyStudentAbsence::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineAbsence::class;
    }
}
