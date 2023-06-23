<?php

namespace Database\Factories;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
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

    public function withStageType(LegacyStageType $stageType, $sequence = 1): static
    {
        $dates = [
            1 => [
                1 => [
                    'data_inicio' => now()->day(15)->month(2),
                    'data_fim' => now()->day(15)->month(12),
                    'dias_letivos' => 200,
                ],
            ],
            2 => [
                1 => [
                    'data_inicio' => now()->day(15)->month(2),
                    'data_fim' => now()->day(15)->month(7),
                    'dias_letivos' => 100,
                ],
                2 => [
                    'data_inicio' => now()->day(15)->month(7),
                    'data_fim' => now()->day(15)->month(12),
                    'dias_letivos' => 100,
                ],
            ],
            3 => [
                1 => [
                    'data_inicio' => now()->day(15)->month(2),
                    'data_fim' => now()->day(15)->month(5),
                    'dias_letivos' => 70,
                ],
                2 => [
                    'data_inicio' => now()->day(15)->month(5),
                    'data_fim' => now()->day(15)->month(8),
                    'dias_letivos' => 60,
                ],
                3 => [
                    'data_inicio' => now()->day(15)->month(8),
                    'data_fim' => now()->day(15)->month(12),
                    'dias_letivos' => 70,
                ],
            ],
            4 => [
                1 => [
                    'data_inicio' => now()->day(15)->month(2),
                    'data_fim' => now()->day(15)->month(5),
                    'dias_letivos' => 50,
                ],
                2 => [
                    'data_inicio' => now()->day(15)->month(5),
                    'data_fim' => now()->day(15)->month(7),
                    'dias_letivos' => 50,
                ],
                3 => [
                    'data_inicio' => now()->day(15)->month(7),
                    'data_fim' => now()->day(15)->month(9),
                    'dias_letivos' => 50,
                ],
                4 => [
                    'data_inicio' => now()->day(15)->month(9),
                    'data_fim' => now()->day(15)->month(12),
                    'dias_letivos' => 50,
                ],
            ],
        ];

        return $this->state([
            'ref_cod_modulo' => $stageType,
            'sequencial' => $sequence,
            'data_inicio' => $dates[$stageType->num_etapas][$sequence]['data_inicio'],
            'data_fim' => $dates[$stageType->num_etapas][$sequence]['data_fim'],
            'dias_letivos' => $dates[$stageType->num_etapas][$sequence]['dias_letivos'],
        ]);
    }
}
