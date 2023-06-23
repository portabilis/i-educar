<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineDescriptiveOpinionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineDescriptiveOpinion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'parecer_aluno_id' => fn () => LegacyStudentDescriptiveOpinionFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'parecer' => $this->faker->text,
            'etapa' => $this->faker->randomDigitNotZero(),
        ];
    }
}
