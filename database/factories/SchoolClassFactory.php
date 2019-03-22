<?php

use Faker\Generator as Faker;
use App\Models\SchoolClass;

$factory->define(SchoolClass::class, function (Faker $faker) {
    return [
        'ref_usuario_cad' => 1,
        'nm_turma' => $faker->words(3, true),
        'max_aluno' => 99,
        'data_cadastro' => now(),
        'ref_cod_turma_tipo' => 1,
        'dias_semana' => [2, 3, 4, 5, 6],
    ];
});

$factory->defineAs(SchoolClass::class, 'morning', function (Faker $faker) use ($factory) {
    $schollClass = $factory->raw(SchoolClass::class);

    return array_merge($schollClass, [
        'hora_inicial' => '07:45',
        'hora_final' => '11:45',
    ]);
});

$factory->defineAs(SchoolClass::class, 'afternoon', function (Faker $faker) use ($factory) {
    $schollClass = $factory->raw(SchoolClass::class);

    return array_merge($schollClass, [
        'hora_inicial' => '13:15',
        'hora_final' => '17:15',
    ]);
});
