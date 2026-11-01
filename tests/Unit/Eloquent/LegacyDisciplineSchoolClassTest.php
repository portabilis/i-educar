<?php

use App\Models\LegacyDisciplineSchoolClass;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineSchoolClass::class));

EloquentModelTestCase::registerModelTests();
