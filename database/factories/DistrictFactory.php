<?php

use App\City;
use App\District;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(District::class, function (Faker $faker) {
    return [
        'city_id' => function () {

            $city = City::query()->inRandomOrder()->first();

            if (empty($city)) {
                $city = factory(City::class)->create();
            }

            return $city->getKey();
        },
        'name' => $faker->name,
        'ibge' => $faker->randomNumber(6),
    ];
});
