<?php

namespace App\Services;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentScore;
use RegraAvaliacao_Model_Nota_TipoValor;

class CopyScoreService
{
    /**
     * @var LegacyRegistration
     */
    private $newRegistration;

    /**
     * @var LegacyRegistration
     */
    private $oldRegistration;

    /**
     * @var LegacyEvaluationRule
     */
    private $newEvaluationRule;

    /**
     * @var LegacyEvaluationRule
     */
    private $oldEvaluationRule;

    /**
     * Copia notas de uma matrícula pra outra
     *
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     */
    public function copyScores(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        $this->newRegistration = $newRegistration;
        $this->oldRegistration = $oldRegistration;

        $this->newEvaluationRule = RegistrationEvaluationRuleService::getEvaluationRule($this->newRegistration);
        $this->oldEvaluationRule = RegistrationEvaluationRuleService::getEvaluationRule($this->oldRegistration);

        if (!$this->compatibleScoreType()) {
            return;
        }

        $studentScore = $this->createStudentScore();
        $this->createScore($studentScore);
    }

    /**
     * Verifica se os tipos de notas das duas regras são iguais
     *
     * @return bool
     */
    private function compatibleScoreType()
    {
        return $this->newEvaluationRule->tipo_nota == $this->oldEvaluationRule->tipo_nota
            && $this->newEvaluationRule->nota_geral_por_etapa == $this->oldEvaluationRule->nota_geral_por_etapa;
    }

    /**
     * Cria o registro em nota_aluno pra nova matrícula
     *
     * @return LegacyStudentScore
     */
    private function createStudentScore()
    {
        return LegacyStudentScore::create(
            [
                'matricula_id' => $this->newRegistration->getKey(),
            ]
        );
    }

    /**
     * Copia as notas para a matrícula nova
     *
     * @param LegacyStudentScore $studentScore
     */
    private function createScore($studentScore)
    {
        if ($this->newEvaluationRule->tipo_nota == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            return;
        }

        if ($this->newEvaluationRule->nota_geral_por_etapa) {
            $this->copyGeneralScore($studentScore);
            return;
        }

        $this->copyDisciplineScore($studentScore);
    }

    /**
     * Copia nota por componente
     *
     * @param $studentScore
     */
    private function copyDisciplineScore($studentScore)
    {
        $scores = $this->oldRegistration->studentScore->scoreByDiscipline;

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
     * @param $studentScore
     */
    private function copyGeneralScore($studentScore)
    {
        $scores = $this->oldRegistration->studentScore->generalScore;

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
