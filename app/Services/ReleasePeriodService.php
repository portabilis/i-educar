<?php

namespace App\Services;

use App\Models\LegacySchoolClass;
use App\Models\ReleasePeriod;
use Exception;

class ReleasePeriodService
{
    /**
     * Verifica se podem ser postadas notas e faltas
     *
     * @param integer $schoolId
     * @param integer $classRoomId
     * @param integer $stage
     * @param integer $year
     * @return bool
     * @throws Exception
     */
    public function canPostNow($schoolId, $classRoomId, $stage, $year)
    {
        $stages = LegacySchoolClass::findOrFail($classRoomId)->stages;
        $firstStage = $stages->first();

        if (empty($firstStage)) {
            throw new Exception('Não foi possível identificar as etapas da turma');
        }

        $stageType = $firstStage->ref_cod_modulo;

        /** @var ReleasePeriod $releasePeriod */
        $releasePeriod = ReleasePeriod::where('year', $year)
            ->where('stage_type_id', $stageType)
            ->where('stage', $stage)
            ->whereHas('schools', function ($schoolsQuery) use ($schoolId) {
                $schoolsQuery->where('school_id', $schoolId);
            })->first();

        if (empty($releasePeriod)) {
            return true;
        }

        return $releasePeriod->periodDates()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }
}
