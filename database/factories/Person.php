<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Person::class, function (Faker $faker) {
    return [
        'nome' => $faker->name,
        'situacao' => 'P',
        'origem_gravacao' => 'U',
        'tipo' => 'F',
        'data_cad' => $faker->dateTime,
        'operacao' => 'I',
        'idsis_cad' => '17',
    ];
});
