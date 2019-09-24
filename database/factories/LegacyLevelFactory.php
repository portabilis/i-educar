<?php

use App\Models\LegacyCourse;
use App\Models\LegacyLevel;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyLevel::class, function (Faker $faker) {
    return [
        'nm_serie' => $faker->words(3, true),
        'ref_usuario_cad' => 1,
        'ref_cod_curso' => factory(LegacyCourse::class)->create(),
        'etapa_curso' => $faker->randomElement([1, 2, 3, 4]),
        'carga_horaria' => 800,
        'data_cadastro' => $faker->dateTime(),
    ];
});
