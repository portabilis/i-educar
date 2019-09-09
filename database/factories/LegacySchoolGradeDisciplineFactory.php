<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacySchoolGradeDiscipline::class, function () {

    return [
        'ref_ref_cod_escola' => factory(LegacySchool::class)->create(),
        'ref_ref_cod_serie' => factory(LegacyLevel::class)->create(),
        'ref_cod_disciplina' => factory(LegacyDiscipline::class)->create(),
        'ativo' => 1,
        'anos_letivos' => '{'.now()->year.'}',
    ];
});
