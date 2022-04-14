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
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(),
            'componente_curricular_id' => LegacyDisciplineFactory::new()->create(),
            'etapa' => $this->faker->randomElement([2, 3, 4]),
        ];
    }
}
