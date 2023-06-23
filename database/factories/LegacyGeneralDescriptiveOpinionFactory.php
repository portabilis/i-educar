<?php

namespace Database\Factories;

use App\Models\LegacyGeneralDescriptiveOpinion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyGeneralDescriptiveOpinionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyGeneralDescriptiveOpinion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'parecer_aluno_id' => fn () => LegacyStudentDescriptiveOpinionFactory::new()->create(),
            'parecer' => $this->faker->text,
            'etapa' => $this->faker->randomElement(['1', '2', '3', '4']),
        ];
    }
}
