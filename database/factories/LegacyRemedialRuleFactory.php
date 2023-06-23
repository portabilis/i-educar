<?php

namespace Database\Factories;

use App\Models\LegacyRemedialRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRemedialRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRemedialRule::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'regra_avaliacao_id' => fn () => LegacyEvaluationRuleFactory::new()->create(),
            'descricao' => $this->faker->text(20),
            'etapas_recuperadas' => $this->faker->randomElement(['1', '2', '3', '4']),
            'substitui_menor_nota' => $this->faker->boolean,
            'media' => $this->faker->randomFloat(1, 0, 10),
            'nota_maxima' => $this->faker->randomFloat(1, 0, 10),
        ];
    }
}
