<?php

use App\Models\AverageFormula;
use App\Models\EvaluationRule;
use App\Models\Institution;
use Faker\Generator as Faker;

$factory->define(EvaluationRule::class, function (Faker $faker) {
    return [
        'formula_media_id' => factory(AverageFormula::class)->create(),
        'instituicao_id' => factory(Institution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'tipo_nota' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_progressao' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_presenca' => $faker->randomElement([1, 2, 3, 4]),
    ];
});
