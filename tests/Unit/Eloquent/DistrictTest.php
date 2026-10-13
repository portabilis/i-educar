<?php

use App\Models\City;
use App\Models\District;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(District::class, relations: [
    'city' => City::class,
]));

EloquentModelTestCase::registerModelTests();
