<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolClassStage::class, function (Faker $faker) {
    return [
        'ref_cod_turma' => factory(LegacySchoolClass::class)->create(),
        'ref_cod_modulo' => factory(LegacyStageType::class)->state('unique')->make(),
        'sequencial' => $faker->numberBetween(1, 9),
        'data_inicio' => now()->subMonths(3),
        'data_fim' => now()->addMonths(3),
        'dias_letivos' => $faker->numberBetween(150, 200),
    ];
});
