<?php

namespace Database\Factories;

use App\Models\LegacyEvaluationRule;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_Nota_TipoValor;
use RegraAvaliacao_Model_TipoPresenca;
use RegraAvaliacao_Model_TipoProgressao;

class LegacyEvaluationRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEvaluationRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'formula_media_id' => LegacyAverageFormulaFactory::new()->create(),
            'instituicao_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'nome' => $this->faker->words(3, true),
            'tipo_nota' => $this->faker->randomElement([1, 2, 3, 4]),
            'tipo_progressao' => $this->faker->randomElement([1, 2, 3, 4]),
            'tipo_presenca' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function withoutScore(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NENHUM,
                'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
                'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
            ]);
        });
    }

    public function mediaPresencaSemRecuperacao(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
                'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA,
                'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
                'media' => 7,
                'porcentagem_presenca' => 75,
                'nota_maxima_geral' => 10,
                'nota_minima_geral' => 0,
            ]);
        });
    }

    public function progressaoContinuadaNotaConceitual(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
                'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
                'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
                'porcentagem_presenca' => 75,
                'nota_maxima_geral' => 10,
                'nota_minima_geral' => 0,
            ]);
        });
    }

    public function progressaoCalculoMediaRecuperacaoPonderada(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
                'formula_recuperacao_id' => LegacyAverageFormulaFactory::new()->weightedAverageCalculation()->create(),
                'tipo_progressao' => RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA,
                'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
                'porcentagem_presenca' => 75,
                'media' => 7,
                'media_recuperacao' => 6,
                'nota_maxima_geral' => 10,
                'nota_minima_geral' => 0,
            ]);
        });
    }
}
