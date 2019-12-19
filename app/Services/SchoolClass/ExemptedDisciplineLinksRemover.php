<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use DB;

class ExemptedDisciplineLinksRemover
{
    public function remove(LegacySchoolClass $schoolClass) : void
    {
        DB::beginTransaction();
        $this->updateAffectedSchoolClassTeacherTimestamps($schoolClass);
        $this->removeSchoolClassTeacherDisciplines($schoolClass);
        DB::commit();
    }

    private function updateAffectedSchoolClassTeacherTimestamps(LegacySchoolClass $schoolClass) : void
    {
        LegacySchoolClassTeacher::where('turma_id', $schoolClass->id)
            ->whereHas('schoolClassTeacherDisciplines', function($query) use ($schoolClass) {
                $query->where('componente_curricular_id', $schoolClass->exempted_discipline_id);
            })
            ->update([
                'updated_at' => DB::raw('now()'),
            ]);
    }

    private function removeSchoolClassTeacherDisciplines(LegacySchoolClass $schoolClass) : void
    {
        LegacySchoolClassTeacherDiscipline::query()
            ->whereHas('schoolClassTeacher', function($query) use ($schoolClass) {
                $query->where('turma_id', $schoolClass->id);
            })
            ->where('componente_curricular_id', $schoolClass->exempted_discipline_id)
            ->delete();
    }
}
