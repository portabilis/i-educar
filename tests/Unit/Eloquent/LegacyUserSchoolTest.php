<?php

use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Models\LegacyUserSchool;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyUserSchool::class, relations: [
    'school' => LegacySchool::class,
    'user' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
