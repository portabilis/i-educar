<?php

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyUser;

$factory->define(LegacySchoolAcademicYear::class, function () {
    return [
        'ref_cod_escola' => factory(LegacySchool::class)->create(),
        'ano' => now()->year,
        'ref_usuario_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'andamento' => 1,
        'data_cadastro' => now(),
    ];
});
