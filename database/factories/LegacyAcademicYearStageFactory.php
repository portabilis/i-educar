<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyAcademicYearStage::class, function (Faker $faker) {

    $schooAcademicYear = factory(LegacySchoolAcademicYear::class)->create();

    return [
        'ref_ano' => $schooAcademicYear->ano,
        'ref_ref_cod_escola' => $schooAcademicYear->ref_cod_escola,
        'sequencial' => 1,
        'ref_cod_modulo' => factory(LegacyStageType::class)->states('unique')->make(),
        'data_inicio' => now()->subMonths(3),
        'data_fim' => now()->addMonths(3),
        'dias_letivos' => $faker->numberBetween(150, 200),
    ];
});
