<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClassType;
use App\Models\LegacyUser;
use Faker\Generator as Faker;

$factory->define(LegacySchoolClass::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'nm_turma' => $name = $faker->colorName,
        'sgl_turma' => mb_substr($name, 0, 3),
        'max_aluno' => $faker->numberBetween(10, 25),
        'data_cadastro' => now(),
        'ref_cod_turma_tipo' => factory(LegacySchoolClassType::class)->state('unique')->make(),
    ];
});
