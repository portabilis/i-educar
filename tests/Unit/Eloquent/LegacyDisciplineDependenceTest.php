<?php

use App\Models\LegacyDisciplineDependence;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineDependence::class));

EloquentModelTestCase::registerModelTests();
