<?php

namespace Database\Factories;

use App\Models\LegacyStudentAbsence;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyStudentAbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudentAbsence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'matricula_id' => fn () => LegacyRegistrationFactory::new()->create(),
            'tipo_falta' => $this->faker->numberBetween(1, 2),
        ];
    }
}
