<?php

namespace App\Services;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRegistration;

class RegistrationEvaluationRuleService
{
    /**
     * @param LegacyRegistration $registration
     *
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule(LegacyRegistration $registration)
    {
        $escolaUsaRegraDiferenciada = $registration->school->utiliza_regra_diferenciada;
        $level = $registration->level;
        $evaluationRuleLevel = $level->evaluationRules()
            ->wherePivot('ano_letivo', $registration->ano)
            ->first();

        if ($escolaUsaRegraDiferenciada && !empty($evaluationRuleLevel->pivot->regra_avaliacao_diferenciada_id)) {
            $evaluationRuleLevel = LegacyEvaluationRule::find($evaluationRuleLevel->pivot->regra_avaliacao_diferenciada_id);
        }

        if ($registration->student->person->considerableDeficiencies()->exists() &&
            $evaluationRuleLevel->regra_diferenciada_id) {
            return LegacyEvaluationRule::find($evaluationRuleLevel->regra_diferenciada_id);
        }

        return $evaluationRuleLevel;
    }
}
