<?php

namespace App\Services\Discipline;

use App\Models\LegacyScoreExam;
use Illuminate\Database\Eloquent\Builder;

class MoveDataScoreExam implements MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        return LegacyScoreExam::query()
            ->where('ref_cod_componente_curricular', $disciplineFrom)
            ->whereHas('registration', function (Builder $registrationQuery) use ($gradeId, $year) {
                $registrationQuery->where('ano', $year);

                if ($gradeId) {
                    $registrationQuery->where('ref_ref_cod_serie', $gradeId);
                }
            })
            ->update(['ref_cod_componente_curricular' => $disciplineTo]);
    }
}
