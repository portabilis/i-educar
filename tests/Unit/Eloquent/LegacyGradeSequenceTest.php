<?php

use App\Models\LegacyGrade;
use App\Models\LegacyGradeSequence;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGradeSequence::class, relations: [
    'gradeOrigin' => LegacyGrade::class,
    'gradeDestiny' => LegacyGrade::class,
]));

EloquentModelTestCase::registerModelTests();
