<?php

use App\Models\City;
use App\Models\State;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(City::class, function (Faker $faker) {
    return [
        'state_id' => factory(State::class)->create(),
        'name' => $faker->city,
        'ibge_code' => $faker->numerify('########'),
    ];
});
