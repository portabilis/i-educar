<?php

use Faker\Generator as Faker;

$factory->define(\App\Entities\Employee::class, function (Faker $faker) {
    return [
        'ref_cod_pessoa_fj' => function () {
            return factory(\App\Entities\Individual::class)->create()->idpes;
        },
        'matricula' => $faker->randomDigitNotNull,
        'senha' => $faker->randomDigitNotNull,
        'ativo' => 1
    ];
});
