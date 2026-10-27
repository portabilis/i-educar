<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyRace;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRace::class, relations: [
    'individual' => LegacyIndividual::class,
]));

EloquentModelTestCase::registerModelTests();
