<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegimeType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyCourse::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'ref_cod_tipo_regime' => factory(LegacyRegimeType::class)->create(),
        'ref_cod_nivel_ensino' => factory(LegacyEducationLevel::class)->create(),
        'ref_cod_tipo_ensino' => factory(LegacyEducationType::class)->create(),
        'nm_curso' => $faker->words(3, true),
        'sgl_curso' => $faker->word,
        'qtd_etapas' => $faker->randomElement([2, 3, 4]),
        'carga_horaria' => 800,
        'data_cadastro' => now(),
        'ref_cod_instituicao' => factory(LegacyInstitution::class)->states('unique')->make(),
        'hora_falta' => 0.75,
    ];
});

$factory->defineAs(LegacyCourse::class, 'padrao-ano-escolar', function (Faker $faker) use ($factory) {
    $course = $factory->raw(LegacyCourse::class);

    return array_merge($course, [
        'padrao_ano_escolar' => 1,
    ]);
});
