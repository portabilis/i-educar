<?php

use App\Models\Person;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(PersonHasPlace::class, relations: [
    'place' => Place::class,
    'person' => Person::class,
]));

EloquentModelTestCase::registerModelTests();
