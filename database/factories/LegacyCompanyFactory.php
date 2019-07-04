<?php

use App\Models\LegacyCompany;
use App\Models\LegacyPerson;
use Faker\Generator as Faker;

$factory->define(LegacyCompany::class, function (Faker $faker) {
    return [
        'idpes' => factory(LegacyPerson::class)->create(['tipo' => 'J']),
        'cnpj' => $faker->randomNumber(8),
        'origem_gravacao' => $faker->randomElement(['M', 'U', 'C', 'O']),
        'operacao' => $faker->randomElement(['I', 'A', 'E']),
        'idsis_cad' => 1,
        'data_cad' => now(),
        'fantasia' => $faker->name,
    ];
});
