<?php

namespace App\Services;

use App\Models\LegacySchoolClass;
use App\Models\ReleasePeriod;
use Exception;

class ReleasePeriodService
{
    /**
     * @param $schoolId
     * @param $classRoomId
     * @param $stage
     * @param $year
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

        $period = $releasePeriod->periodDates()
            ->where('start_date', '<=', 'NOW()')
            ->where('end_date', '>=', 'NOW()')
            ->exists();

        if ($period) {
            return true;
        }

        return false;
    }
}
