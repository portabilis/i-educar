<?php

use App\Models\LegacyStudent;
use App\Models\LegacyStudentHistoricalHeightWeight;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentHistoricalHeightWeight::class, relations: [
    'student' => LegacyStudent::class,
]));

EloquentModelTestCase::registerModelTests();
