<?php

namespace App\Services\Discipline;

use App\Models\LegacyDisciplineExemption;
use Illuminate\Database\Eloquent\Builder;

class MoveDataDisciplineExemption implements MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        return LegacyDisciplineExemption::query()
            ->where('ref_cod_disciplina', $disciplineFrom)
            ->whereHas('registration', function (Builder $registrationQuery) use ($gradeId, $year) {
                $registrationQuery->where('ano', $year);

                if ($gradeId) {
                    $registrationQuery->where('ref_ref_cod_serie', $gradeId);
                }
            })
            ->update(['ref_cod_disciplina' => $disciplineTo]);
    }
}
