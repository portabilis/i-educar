<?php

use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolStage;
use App\Models\LegacyStageType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolStage::class, function (Faker $faker) {

    $schoolAcademicYear = factory(LegacySchoolAcademicYear::class)->create();
    $stageType = factory(LegacyStageType::class)->state('unique')->make();

    return [
        'ref_ano' => now()->year,
        'ref_ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
        'sequencial' => $faker->unique()->numberBetween(1, 9),
        'ref_cod_modulo' => $stageType->getKey(),
        'data_inicio' => now()->setDate(2019, 2, 1),
        'data_fim' => now()->setDate(2019, 11, 30),
        'dias_letivos' => $faker->numberBetween(150, 200),
    ];
});
