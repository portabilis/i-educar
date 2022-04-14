<?php

namespace App\Services;

use App\Contracts\CopyRegistrationData;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentScore;
use RegraAvaliacao_Model_Nota_TipoValor;

class CopyScoreService implements CopyRegistrationData
{
    /**
     * @var RegistrationEvaluationRuleService
     */
    private $service;

    /**
     * @param RegistrationEvaluationRuleService $service
     */
    public function __construct(RegistrationEvaluationRuleService $service)
    {
        $this->service = $service;
    }

    /**
     * Copia notas de uma matrícula pra outra
     *
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     */
    public function copy(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    ) {
        $newEvaluationRule = $this->service->getEvaluationRule($newRegistration);
        $oldEvaluationRule = $this->service->getEvaluationRule($oldRegistration);

        if (!$this->compatibleScoreType($newEvaluationRule, $oldEvaluationRule)) {
            return;
        }

        $studentScore = $this->createStudentScore($newRegistration);

        $this->createScore($studentScore, $newEvaluationRule, $oldRegistration);
    }

    /**
     * Verifica se os tipos de notas das duas regras são iguais
     *
     * @param LegacyEvaluationRule $newEvaluationRule
     * @param LegacyEvaluationRule $oldEvaluationRule
     *
     * @return bool
     */
    private function compatibleScoreType(
        LegacyEvaluationRule $newEvaluationRule,
        LegacyEvaluationRule $oldEvaluationRule
    ) {
        return $newEvaluationRule->tipo_nota == $oldEvaluationRule->tipo_nota
            && $newEvaluationRule->nota_geral_por_etapa == $oldEvaluationRule->nota_geral_por_etapa;
    }

    /**
     * Cria o registro em nota_aluno pra nova matrícula
     *
     * @param LegacyRegistration $newRegistration
     *
     * @return LegacyStudentScore
     */
    private function createStudentScore(LegacyRegistration $newRegistration)
    {
        return LegacyStudentScore::create(
            [
                'matricula_id' => $newRegistration->getKey(),
            ]
        );
    }

    /**
     * Copia as notas para a matrícula nova
     *
     * @param LegacyStudentScore   $studentScore
     * @param LegacyEvaluationRule $newEvaluationRule
     * @param LegacyRegistration   $oldRegistration
     */
    private function createScore(
        LegacyStudentScore $studentScore,
        LegacyEvaluationRule $newEvaluationRule,
        LegacyRegistration $oldRegistration
    ) {
        if ($newEvaluationRule->tipo_nota == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            return;
        }

        if ($newEvaluationRule->nota_geral_por_etapa) {
            $this->copyGeneralScore($studentScore, $oldRegistration);

            return;
        }

        $this->copyDisciplineScore($studentScore, $oldRegistration);
    }

    /**
     * Copia nota por componente
     *
     * @param LegacyStudentScore $studentScore
     * @param LegacyRegistration $oldRegistration
     */
    private function copyDisciplineScore(
        LegacyStudentScore $studentScore,
        LegacyRegistration $oldRegistration
    ) {
        $scores = $oldRegistration->studentScore->scoreByDiscipline;

        foreach ($scores as $score) {
            LegacyDisciplineScore::create(
                [
                    'nota_aluno_id' => $studentScore->getKey(),
                    'componente_curricular_id' => $score->componente_curricular_id,
                    'nota' => $score->nota,
                    'nota_arredondada' => $score->nota_arredondada,
                    'etapa' => $score->etapa,
                    'nota_recuperacao' => $score->nota_recuperacao,
                    'nota_original' => $score->nota_original,
                    'nota_recuperacao_especifica' => $score->nota_recuperacao_especifica
                ]
            );
        }
    }

    /**
     * Copia nota geral
     *
     * @param LegacyStudentScore $studentScore
     * @param LegacyRegistration $oldRegistration
     */
    private function copyGeneralScore(
        LegacyStudentScore $studentScore,
        LegacyRegistration $oldRegistration
    ) {
        $scores = $oldRegistration->studentScore->generalScore;

        foreach ($scores as $score) {
            LegacyGeneralScore::create(
                [
                    'nota_aluno_id' => $studentScore->getKey(),
                    'nota' => $score->nota,
                    'nota_arredondada' => $score->nota_arredondada,
                    'etapa' => $score->etapa,
                ]
            );
        }
    }
}
