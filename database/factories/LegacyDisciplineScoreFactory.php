<?php

namespace Database\Factories;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyRegistrationScore;
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
            'nota_aluno_id' => LegacyRegistrationScore::factory()->create(),
            'componente_curricular_id' => LegacyDiscipline::factory()->create(),
            'etapa' => $this->faker->randomElement([2, 3, 4]),
        ];
    }
}
