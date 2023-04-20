<?php

namespace Database\Factories;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
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
        return [
            'escola_ano_letivo_id' => fn () => LegacySchoolAcademicYearFactory::new()->create(),
            'ref_ano' => fn (array $attributes) => LegacySchoolAcademicYear::query()->find($attributes['escola_ano_letivo_id'])->ano,
            'ref_ref_cod_escola' => fn (array $attributes) => LegacySchoolAcademicYear::query()->find($attributes['escola_ano_letivo_id'])->ref_cod_escola,
            'sequencial' => 1,
            'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
            'data_inicio' => now()->subMonths(3),
            'data_fim' => now()->addMonths(3),
            'dias_letivos' => $this->faker->numberBetween(150, 200),
        ];
    }
}
