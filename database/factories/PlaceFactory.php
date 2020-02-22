<?php

use App\Models\City;
use App\Models\Place;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(Place::class, function (Faker $faker) {
    return [
        'city_id' => factory(City::class)->create(),
        'address' => $faker->streetName,
        'number' => $faker->numberBetween(1, 9999),
        'complement' => $faker->boolean ? 'Apto' : null,
        'neighborhood' => $faker->month . ' Neighborhood',
        'postal_code' => $faker->numerify('########'),
    ];
});
