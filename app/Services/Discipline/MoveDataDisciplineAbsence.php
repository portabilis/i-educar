<?php

namespace App\Services\Discipline;

use App\Models\LegacyDisciplineAbsence;
use Illuminate\Database\Eloquent\Builder;

class MoveDataDisciplineAbsence implements MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        return LegacyDisciplineAbsence::query()
            ->where('componente_curricular_id', $disciplineFrom)
            ->whereHas('studentAbsence', function (Builder $registrationScoreQuery) use ($gradeId, $year) {
                $registrationScoreQuery->whereHas('registration', function (Builder $registrationQuery) use ($gradeId, $year) {
                    $registrationQuery->where('ano', $year);

                    if ($gradeId) {
                        $registrationQuery->where('ref_ref_cod_serie', $gradeId);
                    }
                });
            })
            ->update(['componente_curricular_id' => $disciplineTo]);
    }
}
