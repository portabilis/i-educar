<?php

namespace Database\Factories;

use App\Models\LegacyAcademicYearStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyAcademicYearStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyAcademicYearStage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schooAcademicYear = LegacySchoolAcademicYearFactory::new()->create();

        return [
            'ref_ano' => $schooAcademicYear->ano,
            'ref_ref_cod_escola' => $schooAcademicYear->ref_cod_escola,
            'sequencial' => 1,
            'ref_cod_modulo' => LegacyStageTypeFactory::new()->unique()->make(),
            'data_inicio' => now()->subMonths(3),
            'data_fim' => now()->addMonths(3),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
