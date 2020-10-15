<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyLevel;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(App\Models\LegacyEvaluationRuleGradeYear::class, function (Faker $faker) {
    return [
        'serie_id' => factory(LegacyLevel::class)->create(),
        'regra_avaliacao_id' => factory(LegacyEvaluationRule::class)->create(),
        'regra_avaliacao_diferenciada_id' => null,
        'ano_letivo' => '{' . now()->year . '}',
    ];
});
