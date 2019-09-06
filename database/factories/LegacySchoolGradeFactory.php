<?php

use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolGrade::class, function (Faker $faker) {

    $schoolCourse = factory(LegacySchoolCourse::class)->create();

    return [
        'ref_cod_escola' => $schoolCourse->school,
        'ref_cod_serie' => factory(LegacyLevel::class)->create([
            'ref_cod_curso' => $schoolCourse->course,
        ]),
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
        'ativo' => 1,
        'anos_letivos' => $schoolCourse->anos_letivos,
    ];
});
