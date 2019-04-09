<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LegacyIndividual::class, function (Faker $faker) {
    return [
        'idpes' => function() {
            return factory(\App\Models\LegacyPerson::class)->create()->idpes;
        },
        'origem_gravacao' => 'U',
        'data_cad' => $faker->dateTime,
        'operacao' => 'I',
        'idsis_cad' => '17',
    ];
});
