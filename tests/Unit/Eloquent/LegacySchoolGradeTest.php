<?php

use App\Models\LegacyGrade;
use App\Models\LegacySchoolGrade;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolGrade::class, relations: [
    'grade' => LegacyGrade::class,
]));

EloquentModelTestCase::registerModelTests();
