<?php

namespace App\Services;

use App\Exceptions\Transfer\MissingAbsenceType;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use RegraAvaliacao_Model_TipoPresenca;

class CopyAbsenceService
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

    public function __construct(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        $this->newRegistration = $newRegistration;
        $this->oldRegistration = $oldRegistration;

        $this->newEvaluationRule = RegistrationEvaluationRuleService::getEvaluationRule($this->newRegistration);
        $this->oldEvaluationRule = RegistrationEvaluationRuleService::getEvaluationRule($this->oldRegistration);
    }

    /**
     *
     */
    public function copyAbsences()
    {
        if (!$this->compatibleAbsenceType()) {
            return;
        }

        $studentAbsence = $this->createStudentAbsence();
        $this->createAbsence($studentAbsence);
    }

    /**
     * Verifica se os tipos de presença das duas regras é igual
     *
     * @return bool
     */
    private function compatibleAbsenceType()
    {
        return $this->newEvaluationRule->tipo_presenca == $this->oldEvaluationRule->tipo_presenca;
    }

    /**
     * Cria o registro em falta_aluno pra nova matrícula
     *
     * @return LegacyStudentAbsence
     */
    private function createStudentAbsence()
    {
        return LegacyStudentAbsence::create(
            [
                'matricula_id' => $this->newRegistration->getKey(),
                'tipo_falta' => $this->newEvaluationRule->tipo_presenca,
            ]
        );
    }

    /**
     * Copia as faltas para a matrícula nova
     *
     * @param LegacyStudentAbsence $studentAbsence
     * @throws MissingAbsenceType
     */
    private function createAbsence($studentAbsence)
    {
        if ($this->newEvaluationRule->tipo_presenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $this->copyDisciplineAbsence($studentAbsence);
            return;
        }

        if ($this->newEvaluationRule->tipo_presenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $this->copyGeneralAbsence($studentAbsence);
            return;
        }

        throw new MissingAbsenceType();
    }

    /**
     * Copia falta por componente
     *
     * @param $studentAbsence
     */
    private function copyDisciplineAbsence($studentAbsence)
    {
        $absences = $this->oldRegistration->studentAbsence->absences;

        foreach ($absences as $absence) {
            LegacyDisciplineAbsence::create(
                [
                    'falta_aluno_id' => $studentAbsence->getKey(),
                    'quantidade' => $absence->quantidade,
                    'componente_curricular_id' => $absence->componente_curricular_id,
                    'etapa' => $absence->etapa,
                ]
            );
        }
    }

    /**
     * Copia falta geral
     *
     * @param $studentAbsence
     */
    private function copyGeneralAbsence($studentAbsence)
    {
        $absences = $this->oldRegistration->studentAbsence->absences;

        foreach ($absences as $absence) {
            LegacyGeneralAbsence::create(
                [
                    'falta_aluno_id' => $studentAbsence->getKey(),
                    'quantidade' => $absence->quantidade,
                    'etapa' => $absence->etapa,
                ]
            );
        }
    }
}
