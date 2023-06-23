<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineScore;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineScore::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nota_aluno_id' => fn () => LegacyRegistrationScoreFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'etapa' => $this->faker->randomElement([1, 2, 3, 4]),
            'nota' => $score = $this->faker->randomFloat(1, 0, 10),
            'nota_arredondada' => $score,
            'nota_recuperacao' => null,
            'nota_original' => $score,
            'nota_recuperacao_especifica' => null,
        ];
    }
}
