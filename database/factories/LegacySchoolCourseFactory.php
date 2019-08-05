<?php

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use App\Models\LegacyUser;
use Faker\Generator as Faker;

$factory->define(LegacySchoolCourse::class, function (Faker $faker) {
    return [
        'ref_cod_escola' => factory(LegacySchool::class)->create(),
        'ref_cod_curso' => factory(LegacyCourse::class)->create(),
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
        'ativo' => 1,
        'autorizacao' => $faker->sentence,
        'anos_letivos' => '{' . now()->format('Y') . '}',
    ];
});
