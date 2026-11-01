<?php

use App\Models\LegacyDeficiency;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDeficiency::class));

EloquentModelTestCase::registerModelTests();
