<?php

use App\Models\LegacyAverageFormula;
use App\Models\LegacyInstitution;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyAverageFormula::class, function (Faker $faker) {
    return [
        'instituicao_id' => factory(LegacyInstitution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'formula_media' => 'Se / Et',
    ];
});

$factory->defineAs(LegacyAverageFormula::class,  'calculo-media-ponderada', function (Faker $faker) use ($factory) {
    $averageFormula = $factory->raw(LegacyAverageFormula::class);
    return array_merge($averageFormula, [
        'formula_media' => $faker->randomElement(['((((Se / Et) * 7) + (Rc * 3)) / 10)', '(((((Se / Et) * 3) + (Rc * 2)) / 5))', '((Rc * 0.4) + (Se / Et * 0.6))']),
        'tipo_formula' => 2,
    ]);
});

$factory->defineAs(LegacyEvaluationRule::class, 'media-presenca-sem-recuperacao', function (Faker $faker) use ($factory) {
    $evaluationRule = $factory->raw(LegacyEvaluationRule::class);

    return array_merge($evaluationRule, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
        'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA,
        'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        'media' => 7,
        'porcentagem_presenca' => 75,
        'nota_maxima_geral' => 10,
        'nota_minima_geral' => 0,

    ]);
});
