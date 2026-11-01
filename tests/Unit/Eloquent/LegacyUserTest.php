<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyUser::class, relations: [
    'type' => LegacyUserType::class,
    'createdByEmployee' => LegacyEmployee::class,
    'deletedByEmployee' => LegacyEmployee::class,
]));

EloquentModelTestCase::registerModelTests();
