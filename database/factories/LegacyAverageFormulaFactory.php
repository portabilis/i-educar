<?php

namespace Database\Factories;

use App\Models\LegacyAverageFormula;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_Nota_TipoValor;
use RegraAvaliacao_Model_TipoPresenca;
use RegraAvaliacao_Model_TipoProgressao;

class LegacyAverageFormulaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyAverageFormula::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'instituicao_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'nome' => $this->faker->words(3, true),
            'formula_media' => 'Se / Et',
        ];
    }

    public function weightedAverageCalculation(): self
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'formula_media' => $this->faker->randomElement(['((((Se / Et) * 7) + (Rc * 3)) / 10)', '(((((Se / Et) * 3) + (Rc * 2)) / 5))', '((Rc * 0.4) + (Se / Et * 0.6))']),
                'tipo_formula' => 2,
            ]);
        });
    }

    public function averageWithoutRemedial(): self
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
}
