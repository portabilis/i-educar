<?php

use App\Models\Enrollment;
use App\Models\LegacySchoolClass;
use App\Models\Registration;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(Enrollment::class, function (Faker $faker) {
    return [
        'ref_cod_matricula' => factory(Registration::class)->create(),
        'ref_cod_turma' => factory(LegacySchoolClass::class)->create(),
        'sequencial' => 0,
        'ref_usuario_cad' => 1,
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1
    ];
});
