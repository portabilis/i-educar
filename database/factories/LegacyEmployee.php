<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LegacyEmployee::class, function (Faker $faker) {
    return [
        'ref_cod_pessoa_fj' => function () {
            return factory(\App\Models\LegacyIndividual::class)->create()->idpes;
        },
        'matricula' => $faker->randomDigitNotNull,
        'senha' => $faker->randomDigitNotNull,
        'ativo' => 1
    ];
});
