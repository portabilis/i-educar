<?php

use App\Models\LegacyCalendarNote;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyCalendarNote::class));

EloquentModelTestCase::registerModelTests();
