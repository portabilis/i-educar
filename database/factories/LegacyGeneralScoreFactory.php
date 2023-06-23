<?php

namespace Database\Factories;

use App\Models\LegacyGeneralScore;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGeneralScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyGeneralScore::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nota_aluno_id' => fn () => LegacyRegistrationScoreFactory::new()->create(),
            'nota' => $this->faker->randomFloat(1, 0, 10),
            'nota_arredondada' => $this->faker->randomFloat(1, 0, 10),
            'etapa' => 1,
        ];
    }
}
