<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\LegacyStudentMedicalRecord;
use Tests\EloquentTestCase;

class LegacyStudentMedicalRecordTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyStudentMedicalRecord::class;
    }
}
