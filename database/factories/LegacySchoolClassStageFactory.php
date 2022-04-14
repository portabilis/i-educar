<?php

namespace Database\Factories;

use App\Models\LegacySchoolClassStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClassStage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_turma' => LegacySchoolClassFactory::new()->create(),
            'ref_cod_modulo' => LegacyStageTypeFactory::new()->unique()->make(),
            'sequencial' => $this->faker->numberBetween(1, 9),
            'data_inicio' => now()->subMonths(3),
            'data_fim' => now()->addMonths(3),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
