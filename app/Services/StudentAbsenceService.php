<?php

namespace App\Services;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use Illuminate\Support\Facades\Cache;

class StudentAbsenceService
{
    /**
     * @param LegacyRegistration $registration
     * @param LegacyEvaluationRule $evaluationRule
     * @return LegacyStudentAbsence
     */
    public function getOrCreateStudentAbsence($registration, $evaluationRule)
    {
        return Cache::store('array')->remember("getOrCreateStudentAbsence:{$registration}", now()->addMinute(), function () use ($registration, $evaluationRule) {
            if ($registration->studentAbsence) {
                return $registration->studentAbsence;
            }

            return $this->createStudentAbsence($registration, $evaluationRule);
        });
    }

    /**
     * @param LegacyRegistration $registration
     * @param LegacyEvaluationRule $evaluationRule
     * @return LegacyStudentAbsence
     */
    private function createStudentAbsence($registration, $evaluationRule)
    {
        return LegacyStudentAbsence::create([
            'matricula_id' => $registration->getKey(),
            'tipo_falta' => $evaluationRule->tipo_presenca,
        ]);
    }
}
