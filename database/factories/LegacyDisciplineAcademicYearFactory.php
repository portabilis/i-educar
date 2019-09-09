<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacySchool;
use Faker\Generator as Faker;

$factory->define(LegacyDisciplineAcademicYear::class, function (Faker $faker) {
    return [
        'componente_curricular_id' => factory(LegacyDiscipline::class)->create(),
        'ano_escolar_id' => factory(LegacySchool::class)->create(),
        'carga_horaria' => $faker->randomNumber(2),
        'tipo_nota' => $faker->randomElement([1,2]),
        'anos_letivos' => '{'.now()->year.'}',
    ];
});
