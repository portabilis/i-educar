<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Serie::class, function (Faker $faker) {
    return [
        'nm_serie' => $faker->words(3, true),
        'ref_usuario_cad' => 1,
        'ref_cod_curso' => 1,
        'etapa_curso' => $faker->randomElement([1,2,3,4]),
        'carga_horaria' => $faker->randomFloat(),
        'data_cadastro' => $faker->dateTime(),
    ];
});
