<?php

use App\Models\Person;
use Faker\Generator as Faker;

$factory->define(Person::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'data_cad' => now(),
        'tipo' => $faker->randomElement(['F', 'J']),
        'situacao' => $faker->randomElement(['A', 'I', 'P']),
        'origem_gravacao' => $faker->randomElement(['M', 'U', 'C', 'O']),
        'operacao' => $faker->randomElement(['I', 'A', 'E']),
        'idsis_cad' => 1,
    ];
});
