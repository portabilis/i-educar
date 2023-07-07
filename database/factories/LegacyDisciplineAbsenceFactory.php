<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineAbsence;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineAbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineAbsence::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'falta_aluno_id' => fn () => LegacyStudentAbsenceFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'quantidade' => $this->faker->numberBetween(0, 15),
            'etapa' => $this->faker->numberBetween(1, 2),
        ];
    }
}
