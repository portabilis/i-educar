<?php

use App\Models\LegacyStudent;
use App\Models\LegacyStudentHousing;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentHousing::class, relations: [
    'student' => LegacyStudent::class,
]));

EloquentModelTestCase::registerModelTests();
