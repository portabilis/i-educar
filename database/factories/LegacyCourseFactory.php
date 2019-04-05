<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegimeType;
use Faker\Generator as Faker;

$factory->define(LegacyCourse::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'ref_cod_tipo_regime' => factory(LegacyRegimeType::class)->create(),
        'ref_cod_nivel_ensino' => factory(LegacyEducationLevel::class)->create(),
        'ref_cod_tipo_ensino' => factory(LegacyEducationType::class)->create(),
        'nm_curso' => $faker->words(3, true),
        'sgl_curso' => $faker->word,
        'qtd_etapas' => $faker->randomElement([2, 3, 4]),
        'carga_horaria' => $faker->randomElement([200, 400, 800]),
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->states('unique')->make(),
    ];
});
