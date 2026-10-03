<?php

namespace Tests\Feature\Api\V2;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStageTypeFactory;

trait CreatesSchoolCalendar
{
    /**
     * Define o calendário da matrícula com a quantidade de etapas informada. O boletim
     * conta as etapas da escola quando o curso segue o ano letivo padrão e as da
     * turma nos demais casos.
     */
    protected function createSchoolCalendar(LegacyRegistration $registration, LegacySchoolClass $schoolClass, int $stages): void
    {
        $stageType = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $registration->school->ref_cod_instituicao,
            'num_etapas' => $stages,
        ]);

        if ($registration->course?->padrao_ano_escolar) {
            $this->createAcademicYearStages($registration, $stageType->getKey(), $stages);

            return;
        }

        LegacySchoolClassStage::query()->where('ref_cod_turma', $schoolClass->getKey())->delete();

        foreach (range(1, $stages) as $stage) {
            LegacySchoolClassStageFactory::new()->create([
                'ref_cod_turma' => $schoolClass,
                'ref_cod_modulo' => $stageType->getKey(),
                'sequencial' => $stage,
            ]);
        }
    }

    private function createAcademicYearStages(LegacyRegistration $registration, int $stageTypeId, int $stages): void
    {
        $academicYear = LegacySchoolAcademicYear::query()
            ->where('ref_cod_escola', $registration->ref_ref_cod_escola)
            ->where('ano', $registration->ano)
            ->first()
            ?? LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $registration->ref_ref_cod_escola,
                'ano' => $registration->ano,
            ]);

        LegacyAcademicYearStage::query()
            ->where('ref_ref_cod_escola', $registration->ref_ref_cod_escola)
            ->where('ref_ano', $registration->ano)
            ->delete();

        foreach (range(1, $stages) as $stage) {
            LegacyAcademicYearStageFactory::new()->create([
                'escola_ano_letivo_id' => $academicYear,
                'ref_cod_modulo' => $stageTypeId,
                'sequencial' => $stage,
            ]);
        }
    }
}
