<?php

use App\Models\LegacyAverageFormula;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyAverageFormula::class));

EloquentModelTestCase::registerModelTests();
