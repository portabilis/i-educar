<?php

use App\Models\LegacyStudent;
use App\Models\LegacyStudentMedicalRecord;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentMedicalRecord::class, relations: [
    'student' => LegacyStudent::class,
]));

EloquentModelTestCase::registerModelTests();
