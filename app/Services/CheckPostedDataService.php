<?php

namespace App\Services;

use App\Models\LegacyGrade;
use App\Models\LegacyStudentAbsence;
use App\Models\LegacyStudentDescriptiveOpinion;
use App\Models\LegacyStudentScore;

class CheckPostedDataService
{
    public function hasDataPostedInGrade($discipline, $level, $year = null, $school = null)
    {
        $schoolClassQuery = LegacyGrade::find($level)->schoolClass();

        if ($year) {
            $schoolClassQuery->where('ano', $year);
        }

        if ($school) {
            $schoolClassQuery->where('ref_ref_cod_escola', $school);
        }

        $schoolClass = $schoolClassQuery->get();

        if (empty($schoolClass)) {
            return false;
        }

        return
            $this->hasAbsencePostedInSchoolClass($discipline, $schoolClass->pluck('cod_turma')) ||
            $this->hasScorePostedInSchoolClass($discipline, $schoolClass->pluck('cod_turma')) ||
            $this->hasDescritiveOpinionInSchoolClass($discipline, $schoolClass->pluck('cod_turma'));
    }

    public function hasAbsencePostedInSchoolClass($discipline, $schoolClass)
    {
        return LegacyStudentAbsence::query()
            ->whereHas('registration', function ($queryRegistration) use ($schoolClass) {
                $queryRegistration->whereHas('enrollments', function ($queryEnrollments) use ($schoolClass) {
                    $queryEnrollments->whereIn('ref_cod_turma', $schoolClass);
                });
            })
            ->whereHas('absencesByDiscipline', function ($queryAbsencesByDiscipline) use ($discipline) {
                $queryAbsencesByDiscipline
                    ->where('componente_curricular_id', $discipline);
            })
            ->exists();
    }

    public function hasScorePostedInSchoolClass($discipline, $schoolClass)
    {
        return LegacyStudentScore::query()
            ->whereHas('registration', function ($queryRegistration) use ($schoolClass) {
                $queryRegistration->whereHas('enrollments', function ($queryEnrollments) use ($schoolClass) {
                    $queryEnrollments->whereIn('ref_cod_turma', $schoolClass);
                });
            })
            ->whereHas('scoreByDiscipline', function ($queryScoreByDiscipline) use ($discipline) {
                $queryScoreByDiscipline
                    ->where('componente_curricular_id', $discipline);
            })
            ->exists();
    }

    public function hasDescritiveOpinionInSchoolClass($discipline, $schoolClass)
    {
        return LegacyStudentDescriptiveOpinion::query()
            ->whereHas('registration', function ($queryRegistration) use ($schoolClass) {
                $queryRegistration->whereHas('enrollments', function ($queryEnrollments) use ($schoolClass) {
                    $queryEnrollments->whereIn('ref_cod_turma', $schoolClass);
                });
            })
            ->whereHas('descriptiveOpinionByDiscipline', function ($queryDescriptiveOpinionByDiscipline) use ($discipline) {
                $queryDescriptiveOpinionByDiscipline
                    ->where('componente_curricular_id', $discipline);
            })
            ->exists();
    }
}
