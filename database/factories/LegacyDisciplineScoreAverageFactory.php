<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineScoreAverage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineScoreAverageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineScoreAverage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nota_aluno_id' => fn () => LegacyRegistrationScoreFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'media' => $this->faker->randomFloat(1, 0, 10),
            'media_arredondada' => $this->faker->randomFloat(1, 0, 10),
            'etapa' => 1,
            'situacao' => 3,
            'bloqueada' => false,
        ];
    }
}
