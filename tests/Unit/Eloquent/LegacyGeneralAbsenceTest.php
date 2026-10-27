<?php

use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyStudentAbsence;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGeneralAbsence::class, relations: [
    'studentAbsence' => LegacyStudentAbsence::class,
]));

EloquentModelTestCase::registerModelTests();
