<?php

use App\Models\Individual;
use App\Models\Person;
use Faker\Generator as Faker;

$factory->define(Individual::class, function (Faker $faker) {
    return [
        'idpes' => factory(Person::class)->create(),
        'data_cad' => now(),
        'operacao' => $faker->randomElement(['I', 'A', 'E']),
        'origem_gravacao' => $faker->randomElement(['M', 'U', 'C', 'O']),
        'idsis_cad' => 1,
    ];
});
