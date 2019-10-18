<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\LegacyEvaluationRule;

class RegistrationEvaluationRuleService
{

    public function __construct(LegacyRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function getEvaluationRule()
    {
        $escolaUsaRegraDiferenciada = $this->registration->school->utiliza_regra_diferenciada;
        $level = $this->registration->level;
        $evaluationRuleLevel = $level->evaluationRules()
            ->wherePivot('ano_letivo', $this->registration->ano)
            ->first();

        if($escolaUsaRegraDiferenciada && !empty($evaluationRuleLevel->pivot->regra_avaliacao_diferenciada_id)) {
            $evaluationRuleLevel = LegacyEvaluationRule::find($evaluationRuleLevel->pivot->regra_avaliacao_diferenciada_id);
        }

        if ($this->registration->student->person->considerableDeficiencies()->exists() &&
            $evaluationRuleLevel->regra_diferenciada_id) {
            return LegacyEvaluationRule::find($evaluationRuleLevel->regra_diferenciada_id);
        }

        return $evaluationRuleLevel;
    }
}
