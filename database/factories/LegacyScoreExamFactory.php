<?php

namespace Database\Factories;

use App\Models\LegacyScoreExam;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyScoreExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyScoreExam::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_componente_curricular' => fn () => LegacyDisciplineFactory::new()->create(),
            'nota_exame' => $this->faker->randomFloat(2, 0, 10),
        ];
    }
}
