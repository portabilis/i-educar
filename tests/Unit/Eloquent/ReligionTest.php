<?php

use App\Models\LegacyIndividual;
use App\Models\Religion;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Religion::class, relations: [
    'individual' => LegacyIndividual::class,
]));

EloquentModelTestCase::registerModelTests();
