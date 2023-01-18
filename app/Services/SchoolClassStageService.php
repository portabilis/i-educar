<?php

namespace App\Services;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Rules\CheckGradesAndAbsencesInStageExists;
use App\Rules\CheckGradesAndAbsencesInStageIDiarioExists;

class SchoolClassStageService
{
    public function store(
        LegacySchoolClass $schoolClass,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $stageId
    ) {
        $this->validate($schoolClass, $startDates);
        $schoolClass->schoolClassStages()->delete();

        $schoolClassStage = $this->buildSchoolClassStages($schoolClass, $startDates, $endDates, $schoolDays, $stageId);

        foreach ($schoolClassStage as $stage) {
            $this->storeStage($stage);
        }
    }

    public function validate(LegacySchoolClass $schoolClass, array $startDates)
    {
        validator(
            ['params' => [
                'schoolClass' => $schoolClass,
                'startDates' => $startDates
                ]
            ],
            [
                'params' => [
                    new CheckGradesAndAbsencesInStageExists(),
                    new CheckGradesAndAbsencesInStageIDiarioExists(),
                ]
            ]
        )->validate();
    }

    private function buildSchoolClassStages(
        LegacySchoolClass $schoolClass,
        array $startDates,
        array $endDates,
        array $schoolDays,
        int $stageId
    ) {
        $schoolClassStage = [];
        foreach ($startDates as $key => $stage) {
            $schoolClassStage[$key]['sequencial'] = $key + 1;
            $schoolClassStage[$key]['ref_cod_turma'] = $schoolClass->cod_turma;
            $schoolClassStage[$key]['ref_cod_modulo'] = $stageId;
            $schoolClassStage[$key]['data_inicio'] = dataToBanco($startDates[$key]);
            $schoolClassStage[$key]['data_fim'] = dataToBanco($endDates[$key]);
            $schoolClassStage[$key]['dias_letivos'] = $schoolDays[$key];
        }

        return $schoolClassStage;
    }

    public function storeStage(array $stage)
    {
        $legacySchoolClassStage = new LegacySchoolClassStage();
        $legacySchoolClassStage->fill($stage);
        $legacySchoolClassStage->save();
    }
}
