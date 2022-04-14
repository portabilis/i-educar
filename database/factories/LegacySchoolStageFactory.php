<?php

namespace Database\Factories;

use App\Models\LegacySchoolStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolStage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schoolAcademicYear = LegacySchoolAcademicYearFactory::new()->create();
        $stageType = LegacyStageTypeFactory::new()->unique()->make();

        return [
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
            'sequencial' => $this->faker->unique()->numberBetween(1, 9),
            'ref_cod_modulo' => $stageType->getKey(),
            'data_inicio' => now()->setDate(2019, 2, 1),
            'data_fim' => now()->setDate(2019, 11, 30),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
