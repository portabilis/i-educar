<?php

namespace App\Services;

use App\Models\LegacyStudentAbsence;
use App\Models\LegacyStudentDescriptiveOpinion;
use App\Models\LegacyStudentScore;

class CheckPostedDataService
{
    public function hasDataPosted($discipline, $level, $year)
    {
        return
            $this->hasAbsencePosted($discipline, $level, $year) ||
            $this->hasScorePosted($discipline, $level, $year) ||
            $this->hasDescritiveOpinion($discipline, $level, $year);
    }

    public function hasAbsencePosted($discipline, $level, $year)
    {
        return LegacyStudentAbsence::query()
            ->whereHas('registration', function ($queryRegistration) use ($level, $year) {
                $queryRegistration
                    ->where('ref_ref_cod_serie', $level)
                    ->where('ativo', 1);
                    if ($year) {
                        $queryRegistration->where('ano', $year);
                    }
            })
            ->whereHas('absencesByDiscipline', function($queryAbsencesByDiscipline) use ($discipline) {
                $queryAbsencesByDiscipline
                    ->where('componente_curricular_id', $discipline);
            })
            ->exists();
    }

    public function hasScorePosted($discipline, $level, $year)
    {
        return LegacyStudentScore::query()
            ->whereHas('registration', function ($queryRegistration) use ($level, $year) {
                $queryRegistration
                    ->where('ref_ref_cod_serie', $level)
                    ->where('ativo', 1);
                    if ($year) {
                        $queryRegistration
                            ->where('ano', $year);
                    }
            })
            ->whereHas('scoreByDiscipline', function ($queryScoreByDiscipline) use ($discipline){
                $queryScoreByDiscipline
                    ->where('componente_curricular_id', $discipline);
            })
            ->exists();
    }

    public function hasDescritiveOpinion($discipline, $level, $year)
    {
        return LegacyStudentDescriptiveOpinion::query()
        ->whereHas('registration', function ($queryRegistration) use ($level, $year) {
            $queryRegistration
                ->where('ref_ref_cod_serie', $level)
                ->where('ativo', 1);
                if ($year) {
                    $queryRegistration
                        ->where('ano', $year);
                }
        })
        ->whereHas('descriptiveOpinionByDiscipline', function ($queryDescriptiveOpinionByDiscipline) use ($discipline){
            $queryDescriptiveOpinionByDiscipline
                ->where('componente_curricular_id', $discipline);
        })
        ->exists();
    }
}
