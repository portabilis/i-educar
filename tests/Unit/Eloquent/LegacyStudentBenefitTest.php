<?php

use App\Models\LegacyBenefit;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentBenefit;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentBenefit::class, relations: [
    'student' => LegacyStudent::class,
    'benefit' => LegacyBenefit::class,
]));

EloquentModelTestCase::registerModelTests();
