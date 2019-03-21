<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Enrollment::class, function (Faker $faker) {
    return [
        'ref_cod_matricula' => factory(\App\Models\Registration::class)->create(),
        'ref_cod_turma' => factory(\App\Models\SchoolClass::class)->create(),
        'sequencial' => 0,
        'ref_usuario_cad' => 1,
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1
    ];
});
