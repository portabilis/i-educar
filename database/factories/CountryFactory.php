<?php

use App\Country;
use Faker\Generator as Faker;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'ibge' => $faker->randomNumber(6),
    ];
});
