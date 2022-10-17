<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use Tests\EloquentTestCase;

class LegacyStudentAbsenceTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentAbsence::class;
    }
}
