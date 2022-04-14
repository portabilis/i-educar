<?php

namespace App\Services\Discipline;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use Illuminate\Database\Eloquent\Builder;

class MoveDataDisciplineDescritiveOpinion implements MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        return LegacyDisciplineDescriptiveOpinion::query()
            ->where('componente_curricular_id', $disciplineFrom)
            ->whereHas('studentDescriptiveOpinion', function (Builder $studentDescriptiveOpinionQuery) use ($gradeId, $year) {
                $studentDescriptiveOpinionQuery->whereHas('registration', function (Builder $registrationQuery) use ($gradeId, $year) {
                    $registrationQuery->where('ano', $year);

                    if ($gradeId) {
                        $registrationQuery->where('ref_ref_cod_serie', $gradeId);
                    }
                });
            })
            ->update(['componente_curricular_id' => $disciplineTo]);
    }
}
