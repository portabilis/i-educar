<?php

use App\Models\Course;
use App\Models\EducationLevel;
use App\Models\EducationType;
use App\Models\Institution;
use App\Models\RegimeType;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'ref_cod_tipo_regime' => factory(RegimeType::class)->create(),
        'ref_cod_nivel_ensino' => factory(EducationLevel::class)->create(),
        'ref_cod_tipo_ensino' => factory(EducationType::class)->create(),
        'nm_curso' => $faker->words(3, true),
        'sgl_curso' => $faker->word,
        'qtd_etapas' => $faker->randomElement([2, 3, 4]),
        'carga_horaria' => $faker->randomElement([200, 400, 800]),
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(Institution::class)->states('unique')->make(),
    ];
});
