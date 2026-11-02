<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyMaritalStatus;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyMaritalStatus::class, relations: [
    'individuals' => LegacyIndividual::class,
]));

EloquentModelTestCase::registerModelTests();
