<?php

namespace Database\Factories;

use App\Models\LegacyGeneralAverage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGeneralAverageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyGeneralAverage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nota_aluno_id' => fn () => LegacyRegistrationScoreFactory::new()->create(),
            'media' => $this->faker->randomFloat(4, 0, 10),
            'media_arredondada' => $this->faker->randomFloat(2, 0, 10),
            'etapa' => 1,
        ];
    }
}
