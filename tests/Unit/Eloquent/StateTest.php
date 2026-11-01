<?php

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(State::class, relations: [
    'country' => Country::class,
    'cities' => City::class,
]));

EloquentModelTestCase::registerModelTests();

test('find by abbreviation', function () {
    $stateReturn = State::findByAbbreviation($this->model->abbreviation);
    $this->assertInstanceOf(State::class, $stateReturn);
    $this->assertArrayHasKey('abbreviation', $stateReturn->toArray());
    $this->assertEquals($stateReturn->abbreviation, $this->model->abbreviation);
});

test('get list key abbreviation', function () {
    $list = State::getListKeyAbbreviation();

    $except = State::orderBy('name')->pluck('name', 'abbreviation');

    $this->assertJsonStringEqualsJsonString($except, $list);
});

test('get name by abbreviation', function () {
    $name = State::getNameByAbbreviation($this->model->abbreviation);
    $expect = $this->model->name;

    $this->assertEquals($expect, $name);
});
