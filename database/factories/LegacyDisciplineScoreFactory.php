<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyRegistrationScore;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyDisciplineScore::class, function (Faker $faker) {
    return [
        'nota_aluno_id' => factory(LegacyRegistrationScore::class)->create(),
        'componente_curricular_id' => factory(LegacyDiscipline::class)->create(),
        'etapa' => $faker->randomElement([2, 3, 4]),
    ];
});
