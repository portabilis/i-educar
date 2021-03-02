<?php

use App\Models\City;
use App\Models\District;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(District::class, function (Faker $faker) {
    return [
        'city_id' => factory(City::class)->create(),
        'name' => $faker->dayOfWeek . ' District',
        'ibge_code' => $faker->numerify('########'),
    ];
});
