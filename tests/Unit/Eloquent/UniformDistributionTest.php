<?php

use App\Models\LegacySchool;
use App\Models\LegacyStudent;
use App\Models\UniformDistribution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(UniformDistribution::class, relations: [
    'student' => LegacyStudent::class,
    'school' => LegacySchool::class,
]));

EloquentModelTestCase::registerModelTests();
