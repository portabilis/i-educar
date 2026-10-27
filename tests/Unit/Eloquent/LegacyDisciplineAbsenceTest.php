<?php

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyStudentAbsence;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineAbsence::class, relations: [
    'studentAbsence' => LegacyStudentAbsence::class,
]));

EloquentModelTestCase::registerModelTests();
