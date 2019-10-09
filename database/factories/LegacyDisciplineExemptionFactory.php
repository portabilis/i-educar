<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyDisciplineExemption::class, function (Faker $faker) {
    return [
        'ref_cod_matricula' => factory(LegacyRegistration::class)->create(),
        'ref_cod_disciplina' => factory(LegacyDiscipline::class)->create(),
        'ref_cod_escola' => factory(LegacySchool::class)->create(),
        'ref_cod_serie' => factory(LegacyLevel::class)->create(),
        'ref_cod_tipo_dispensa' =>  factory(LegacyExemptionType::class)->create(),
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
        'ativo' => 1,
    ];
});
