<?php

use App\Models\LegacyCalendarDay;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyCalendarDay::class, relations: [
    'createdByUser' => LegacyUser::class,
    'deletedByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
