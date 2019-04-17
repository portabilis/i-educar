<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use Faker\Generator as Faker;

$factory->define(LegacyIndividual::class, function (Faker $faker) {
    return [
        'idpes' => factory(LegacyPerson::class)->create(),
        'data_cad' => now(),
        'operacao' => $faker->randomElement(['I', 'A', 'E']),
        'origem_gravacao' => $faker->randomElement(['M', 'U', 'C', 'O']),
        'idsis_cad' => 1,
    ];
});
