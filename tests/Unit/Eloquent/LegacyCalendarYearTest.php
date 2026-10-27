<?php

use App\Models\LegacyCalendarYear;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyCalendarYear::class));

EloquentModelTestCase::registerModelTests();
