<?php

use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ReleasePeriodDate::class, relations: [
    'releasePeriod' => ReleasePeriod::class,
]));

EloquentModelTestCase::registerModelTests();
