<?php

use App\Models\LegacyEducacensoStages;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyEducacensoStages::class));

EloquentModelTestCase::registerModelTests();
