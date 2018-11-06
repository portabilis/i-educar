<?php

use Faker\Generator as Faker;

$factory->define(\App\Entities\Person::class, function (Faker $faker) {
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
