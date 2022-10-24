<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyStudentAbsence;
use Tests\EloquentTestCase;

class LegacyGeneralAbsenceTest extends EloquentTestCase
{
    protected $relations = [
        'studentAbsence' => LegacyStudentAbsence::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGeneralAbsence::class;
    }
}
