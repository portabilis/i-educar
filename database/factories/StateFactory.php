<?php

use App\Models\Country;
use App\Models\State;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/** @var Factory $factory */

$factory->define(State::class, function (Faker $faker) {
    return [
        'country_id' => factory(Country::class)->create(),
        'name' => Str::ucfirst($month = $faker->monthName) . ' ' . Str::ucfirst($color = $faker->colorName),
        'abbreviation' => Str::substr($month, 0, 1) . ' ' . Str::substr($color, 0, 1),
        'ibge_code' => $faker->numerify('########'),
    ];
});
