<?php

namespace App\Services;

use App\Models\LegacyRegistration;

class GlobalAverageService
{
    public function getGlobalAverage(LegacyRegistration $registration)
    {
        $scores = $registration->studentScore->averageByDiscipline->pluck('media');

        return $scores->avg();
    }
}
