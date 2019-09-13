<?php

use Faker\Generator as Faker;
use App\Models\Enrollment;

$factory->define(Enrollment::class, function (Faker $faker) {
    return [
        'ref_cod_matricula' => factory(\App\Models\Registration::class)->create(),
        'ref_cod_turma' => factory(\App\Models\LegacySchoolClass::class)->create(),
        'sequencial' => 0,
        'ref_usuario_cad' => 1,
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1
    ];
});
