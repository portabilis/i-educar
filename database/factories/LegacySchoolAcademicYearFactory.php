<?php

namespace Database\Factories;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolAcademicYearFactory extends Factory
{
    protected $model = LegacySchoolAcademicYear::class;

    public function definition(): array
    {
        return [
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ano' => now()->year,
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'andamento' => 1,
        ];
    }

    public function withSchool(LegacySchool $school): static
    {
        return $this->state([
            'ref_cod_escola' => $school,
        ]);
    }

    public function withStageType(LegacyStageType $stageType): static
    {
        return $this->afterCreating(function (LegacySchoolAcademicYear $schoolAcademicYear) use ($stageType) {
            for ($stage = 1; $stage <= $stageType->num_etapas; $stage++) {
                LegacyAcademicYearStageFactory::new()->withStageType($stageType, sequence: $stage)->create([
                    'escola_ano_letivo_id' => $schoolAcademicYear,
                    'ref_ano' => $schoolAcademicYear->ano,
                    'ref_ref_cod_escola' => $schoolAcademicYear->ref_cod_escola,
                ]);
            }
        });
    }
}
