<?php

use App\Models\LegacyAverageFormula;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use Faker\Generator as Faker;

$factory->define(LegacyEvaluationRule::class, function (Faker $faker) {
    return [
        'formula_media_id' => factory(LegacyAverageFormula::class)->create(),
        'instituicao_id' => factory(LegacyInstitution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'tipo_nota' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_progressao' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_presenca' => $faker->randomElement([1, 2, 3, 4]),
    ];
});
