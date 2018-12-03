<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Individual::class, function (Faker $faker) {
    return [
        'idpes' => function() {
            return factory(\App\Models\Person::class)->create()->idpes;
        },
        'origem_gravacao' => 'U',
        'data_cad' => $faker->dateTime,
        'operacao' => 'I',
        'idsis_cad' => '17',
    ];
});
