<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\EvaluationRule::class, function (Faker $faker) {
    return [
        'instituicao_id' => 1,
        'nome' => $faker->words(3, true),
        'formula_media_id' => 1,
        'tipo_nota' => $faker->randomElement([1,2,3,4]),
        'tipo_progressao' => $faker->randomElement([1,2,3,4]),
        'tipo_presenca' => $faker->randomElement([1,2,3,4]),
    ];
});
