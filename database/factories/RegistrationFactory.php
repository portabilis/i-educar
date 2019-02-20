<?php

use App\Models\Registration;
use App\Models\Student;
use Faker\Generator as Faker;

$factory->define(Registration::class, function (Faker $faker) {
    return [
        'ref_cod_aluno' => factory(Student::class)->create(),
        'data_cadastro' => now(),
        'ano' => now()->year,
        'ref_usuario_cad' => 1,
    ];
});
