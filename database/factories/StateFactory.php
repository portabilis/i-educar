<?php

use App\Country;
use App\State;
use Faker\Generator as Faker;

$factory->define(State::class, function (Faker $faker) {
    return [
        'country_id' => function () use ($faker) {

            $country = Country::query()->inRandomOrder()->first();

            if (empty($country) || $faker->boolean()) {
                $country = factory(Country::class)->create();
            }

            return $country->getKey();
        },
        'name' => $faker->colorName . ' State',
        'abbreviation' => function () use ($faker) {
            return $faker->unique()->randomElement([
                'AA', 'BB', 'CC', 'DD', 'EE', 'FF', 'GG', 'HH', 'II', 'JJ', 'KK',
                'LL', 'MM', 'NN', 'OO', 'PP', 'QQ', 'RR', 'SS', 'TT', 'UU', 'VV',
                'WW', 'XX', 'YY', 'ZZ'
            ]);
        },
        'ibge' => $faker->randomNumber(6),
    ];
});
