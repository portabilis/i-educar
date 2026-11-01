<?php

use App\Models\LegacyGeneralScore;
use App\Models\LegacyStudentScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGeneralScore::class, relations: [
    'studentScore' => LegacyStudentScore::class,
]));

EloquentModelTestCase::registerModelTests();
