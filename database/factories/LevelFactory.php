<?php

use App\Models\Course;
use App\Models\Level;
use Faker\Generator as Faker;

$factory->define(Level::class, function (Faker $faker) {
    return [
        'nm_serie' => $faker->words(3, true),
        'ref_usuario_cad' => 1,
        'ref_cod_curso' => factory(Course::class)->create(),
        'etapa_curso' => $faker->randomElement([1,2,3,4]),
        'carga_horaria' => $faker->randomFloat(),
        'data_cadastro' => $faker->dateTime(),
    ];
});
