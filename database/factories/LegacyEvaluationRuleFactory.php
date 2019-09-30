<?php

use App\Models\LegacyAverageFormula;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

require_once __DIR__ . '/../../ieducar/modules/RegraAvaliacao/Model/Nota/TipoValor.php';
require_once __DIR__ . '/../../ieducar/modules/RegraAvaliacao/Model/TipoProgressao.php';
require_once __DIR__ . '/../../ieducar/modules/RegraAvaliacao/Model/TipoPresenca.php';

$factory->define(LegacyEvaluationRule::class, function (Faker $faker) {
    return [
        'formula_media_id' => factory(LegacyAverageFormula::class)->create(),
        'instituicao_id' => factory(LegacyInstitution::class)->states('unique')->make(),
        'nome' => $faker->words(3, true),
        'tipo_nota' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_progressao' => $faker->randomElement([1, 2, 3, 4]),
        'tipo_presenca' => $faker->randomElement([1, 2]),
    ];
});

$factory->defineAs(LegacyEvaluationRule::class, 'without-score', function (Faker $faker) use ($factory) {
    $evaluationRule = $factory->raw(LegacyEvaluationRule::class);

    return array_merge($evaluationRule, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NENHUM,
        'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
        'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
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

$factory->defineAs(LegacyEvaluationRule::class, 'progressao-continuada-nota-conceitual', function () use ($factory) {
    $evaluationRule = $factory->raw(LegacyEvaluationRule::class);

    return array_merge($evaluationRule, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
        'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
        'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        'porcentagem_presenca' => 75,
        'nota_maxima_geral' => 10,
        'nota_minima_geral' => 0,

    ]);
});

$factory->defineAs(LegacyEvaluationRule::class, 'progressao-calculo-media-recuperacao-ponderada', function () use ($factory) {
    $evaluationRule = $factory->raw(LegacyEvaluationRule::class);

    return array_merge($evaluationRule, [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
        'formula_recuperacao_id' => factory(LegacyAverageFormula::class, 'calculo-media-ponderada')->create(),
        'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA,
        'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        'porcentagem_presenca' => 75,
        'media' => 7,
        'media_recuperacao' => 6,
        'nota_maxima_geral' => 10,
        'nota_minima_geral' => 0,
    ]);
});
