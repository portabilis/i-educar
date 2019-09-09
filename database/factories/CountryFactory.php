<?php

use App\Country;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(Country::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'ibge' => $faker->randomNumber(6),
    ];
});
