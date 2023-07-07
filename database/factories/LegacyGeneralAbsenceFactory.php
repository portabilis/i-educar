<?php

namespace Database\Factories;

use App\Models\LegacyGeneralAbsence;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGeneralAbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyGeneralAbsence::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'falta_aluno_id' => fn () => LegacyStudentAbsenceFactory::new()->create(),
            'quantidade' => $this->faker->randomDigitNotZero(),
            'etapa' => 1,
        ];
    }
}
