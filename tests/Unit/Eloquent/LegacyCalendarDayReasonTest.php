<?php

use App\Models\LegacyCalendarDayReason;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyCalendarDayReason::class));

EloquentModelTestCase::registerModelTests();
