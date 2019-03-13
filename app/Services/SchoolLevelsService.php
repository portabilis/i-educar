<?php

namespace App\Services;

use App\Models\LegacyLevel;

class SchoolLevelsService
{
    /**
     * Retorna as regras de avaliação da série
     *
     * @param $levelId
     * @return EvaluationRule[]
     */
    public function getEvaluationRules($levelId)
    {
        $level = LegacyLevel::with('evaluationRules')->find($levelId);

        if (empty($level)) {
            return [];
        }

        return $level->evaluationRules;
    }

    /**
     * Verifica se a regra de avaliação da série permite definir componentes por etapa
     *
     * @param $levelId
     * @param $academicYear
     * @return bool
     */
    public function levelAllowDefineDisciplinePerStage($levelId, $academicYear)
    {
        /** @var LegacyLevel $level */
        $level = LegacyLevel::with('evaluationRules')
            ->whereCodSerie($levelId)
            ->get()
            ->first();

        if (empty($level)) {
            return false;
        }

        $evaluationRule = $level->evaluationRules()
            ->wherePivot('ano_letivo', $academicYear)
            ->get()
            ->first();

        if (empty($evaluationRule)) {
            return false;
        }

        return $evaluationRule->definir_componente_etapa == 1;
    }


}
