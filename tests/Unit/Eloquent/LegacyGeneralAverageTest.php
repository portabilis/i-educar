<?php

use App\Models\LegacyGeneralAverage;
use App\Models\LegacyStudentScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGeneralAverage::class, relations: [
    'studentScore' => LegacyStudentScore::class,
]));

EloquentModelTestCase::registerModelTests();
