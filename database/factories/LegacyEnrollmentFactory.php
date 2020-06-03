<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyEnrollment::class, function (Faker $faker) {
    return [
        'ref_cod_matricula' => factory(LegacyRegistration::class)->create(),
        'ref_cod_turma' => factory(LegacySchoolClass::class)->create(),
        'sequencial' => 1,
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
    ];
});

$factory->defineAs(LegacyEnrollment::class, 'active', function (Faker $faker) use ($factory) {
    $enrollment = $factory->raw(LegacyEnrollment::class);

    return array_merge($enrollment, [
        'ativo' => 1
    ]);
});

$factory->defineAs(LegacyEnrollment::class, 'inactive', function (Faker $faker) use ($factory) {
    $enrollment = $factory->raw(LegacyEnrollment::class);

    return array_merge($enrollment, [
        'ativo' => 0
    ]);
});