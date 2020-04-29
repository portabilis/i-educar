<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;

class CheckPostedDataService
{
    public function hasDataPosted($discipline, $level, $year)
    {
        return
            $this->hasAbsencePosted($discipline, $level, $year);
    }

    public function hasAbsencePosted($discipline, $level, $year)
    {
        return LegacyStudentAbsence::query()
            ->whereHas('registration', function ($queryRegistration) use ($level, $year) {
                $queryRegistration
                    ->where('ref_ref_cod_serie', $level)
                    ->where('ativo', 1)
                    ->where('ano', $year);
            })->exists();
    }

    public function hasScorePosted($discipline, $level, $year)
    {

    }

    public function hasDescritiveOpinion($discipline, $level, $year)
    {

    }
}
