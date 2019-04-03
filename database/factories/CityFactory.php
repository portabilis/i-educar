<?php

use App\State;
use Faker\Generator as Faker;

$factory->define(App\City::class, function (Faker $faker) {
    return [
        'state_id' => function () {

            $state = State::query()->inRandomOrder()->first();

            if (empty($state)) {
                $state = factory(State::class)->create();
            }

            return $state->getKey();
        },
        'name' => $faker->city,
        'ibge' => $faker->randomNumber(6),
    ];
});
