<?php

use App\Models\City;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Place::class, relations: [
    'city' => City::class,
    'personHasPlace' => PersonHasPlace::class,
]));

EloquentModelTestCase::registerModelTests();
