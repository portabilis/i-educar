<?php

use App\Models\Country;
use App\Models\State;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Country::class, relations: [
    'states' => State::class,
]));

EloquentModelTestCase::registerModelTests();
