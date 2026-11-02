<?php

use App\Models\City;
use App\Models\District;
use App\Models\Place;
use App\Models\State;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(City::class, relations: [
    'state' => State::class,
    'districts' => District::class,
    'places' => Place::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $query = City::queryFindByName($this->model->name)->first();

    $this->assertEquals($this->model->name, $query->name);

    $query = City::getNameById($this->model->id);

    $this->assertEquals($this->model->name, $query);
});
