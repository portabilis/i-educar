<?php

namespace Database\Factories;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEvaluationRuleGradeYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEvaluationRuleGradeYear::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'serie_id' => LegacyLevel::factory()->create(),
            'regra_avaliacao_id' => LegacyEvaluationRule::factory()->create(),
            'regra_avaliacao_diferenciada_id' => null,
            'ano_letivo' => '{' . now()->year . '}',
        ];
    }
}
