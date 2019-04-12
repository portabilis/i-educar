<?php

use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyUser;
use Faker\Generator as Faker;

$factory->define(LegacySchoolGrade::class, function (Faker $faker) {
    return [
        'ref_cod_escola' => factory(LegacySchool::class)->create(),
        'ref_cod_serie' => factory(LegacyLevel::class)->create(),
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cadastro' => now(),
    ];
});
