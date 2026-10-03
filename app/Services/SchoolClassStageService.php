<?php

namespace App\Services;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Rules\CheckGradesAndAbsencesInStageExists;
use App\Rules\CheckGradesAndAbsencesInStageIDiarioExists;
use Illuminate\Support\Facades\DB;

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
                'startDates' => $startDates,
            ],
            ],
            [
                'params' => [
                    new CheckGradesAndAbsencesInStageExists,
                    new CheckGradesAndAbsencesInStageIDiarioExists,
                ],
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
        $legacySchoolClassStage = new LegacySchoolClassStage;
        $legacySchoolClassStage->fill($stage);
        $legacySchoolClassStage->save();
    }

    public function updateByCourse(int $course, int $year, bool $isStandardCalendar): void
    {
        DB::beginTransaction();

        $this->deleteByCourseAndYear($course, $year);

        if (!$isStandardCalendar) {
            $this->copyFromSchoolByCourseAndYear($course, $year);
        }

        DB::commit();
    }

    private function deleteByCourseAndYear(int $course, int $year): void
    {
        LegacySchoolClassStage::query()
            ->whereHas('schoolClass', fn ($q) => $q->whereMainCourse($course)->whereYearEq($year))
            ->delete();
    }

    private function copyFromSchoolByCourseAndYear(int $course, int $year): void
    {
        $schoolStages = LegacySchoolClass::query()
            ->join('pmieducar.ano_letivo_modulo', function ($j) {
                $j->on('pmieducar.ano_letivo_modulo.ref_ref_cod_escola', 'pmieducar.turma.ref_ref_cod_escola');
                $j->on('pmieducar.ano_letivo_modulo.ref_ano', 'pmieducar.turma.ano');
            })
            ->whereMainCourse($course)
            ->whereYearEq($year)
            ->select([
                'cod_turma',
                'ref_cod_modulo',
                'sequencial',
                'data_inicio',
                'data_fim',
                'dias_letivos',
            ]);

        LegacySchoolClassStage::query()->insertUsing([
            'ref_cod_turma',
            'ref_cod_modulo',
            'sequencial',
            'data_inicio',
            'data_fim',
            'dias_letivos',
        ], $schoolStages);
    }
}
