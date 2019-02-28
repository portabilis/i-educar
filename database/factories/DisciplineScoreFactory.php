<?php

use App\Models\Discipline;
use App\Models\DisciplineScore;
use App\Models\RegistrationScore;
use Faker\Generator as Faker;

$factory->define(DisciplineScore::class, function (Faker $faker) {
    return [
        'nota_aluno_id' => factory(RegistrationScore::class)->create(),
        'componente_curricular_id' => factory(Discipline::class)->create(),
        'etapa' => $faker->randomElement([2, 3, 4]),
    ];
});
