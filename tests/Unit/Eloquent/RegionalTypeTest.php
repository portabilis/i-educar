<?php

use App\Models\RegionalType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(RegionalType::class));

EloquentModelTestCase::registerModelTests();
