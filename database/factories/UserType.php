<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\UserType::class, function (Faker $faker) {
    return [
        'nm_tipo' => $faker->firstName,
        'nivel' => $faker->randomElement([1,2,3,4]),
        'ref_funcionario_cad' => function() {
            return factory(\App\Models\Employee::class)->create()->ref_cod_pessoa_fj;
        },
        'data_cadastro' => $faker->dateTime,
    ];
});
