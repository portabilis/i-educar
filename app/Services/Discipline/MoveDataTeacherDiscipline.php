<?php

namespace App\Services\Discipline;

use App\Models\LegacySchoolClassTeacherDiscipline;
use Illuminate\Database\Eloquent\Builder;

class MoveDataTeacherDiscipline implements MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        return LegacySchoolClassTeacherDiscipline::query()
            ->where('componente_curricular_id', $disciplineFrom)
            ->whereHas('schoolClassTeacher', function (Builder $schoolClassTeacherQuery) use ($gradeId, $year) {
                $schoolClassTeacherQuery->whereHas('schoolClass', function (Builder $schoolClassQuery) use ($gradeId, $year) {
                    $schoolClassQuery->where('ano', $year);

                    if ($gradeId) {
                        $schoolClassQuery->where('ref_ref_cod_serie', $gradeId);
                    }
                });
            })->update(['componente_curricular_id' => $disciplineTo]);
    }
}
