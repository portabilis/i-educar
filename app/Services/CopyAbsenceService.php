<?php

namespace App\Services;

use App\Contracts\CopyRegistrationData;
use App\Exceptions\Transfer\MissingAbsenceType;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use RegraAvaliacao_Model_TipoPresenca;

class CopyAbsenceService implements CopyRegistrationData
{
    /**
     * @var RegistrationEvaluationRuleService
     */
    private $service;

    /**
     * CopyAbsenceService constructor.
     *
     * @param RegistrationEvaluationRuleService $service
     */
    public function __construct(RegistrationEvaluationRuleService $service)
    {
        $this->service = $service;
    }

    /**
     * Copia faltas de uma matrícula pra outra
     *
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     *
     * @throws MissingAbsenceType
     */
    public function copy(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        $newEvaluationRule = $this->service->getEvaluationRule($newRegistration);
        $oldEvaluationRule = $this->service->getEvaluationRule($oldRegistration);

        if (!$this->compatibleAbsenceType($newEvaluationRule, $oldEvaluationRule)) {
            return;
        }

        $studentAbsence = $this->createStudentAbsence($newRegistration, $newEvaluationRule);

        $this->createAbsence($studentAbsence, $oldRegistration, $newEvaluationRule);
    }

    /**
     * Verifica se os tipos de presença das duas regras é igual
     *
     * @param LegacyEvaluationRule $newEvaluationRule
     * @param LegacyEvaluationRule $oldEvaluationRule
     *
     * @return bool
     */
    private function compatibleAbsenceType($newEvaluationRule, $oldEvaluationRule)
    {
        return $newEvaluationRule->tipo_presenca == $oldEvaluationRule->tipo_presenca;
    }

    /**
     * Cria o registro em falta_aluno pra nova matrícula
     *
     * @param LegacyRegistration   $newRegistration
     * @param LegacyEvaluationRule $newEvaluationRule
     *
     * @return LegacyStudentAbsence
     */
    private function createStudentAbsence($newRegistration, $newEvaluationRule)
    {
        return LegacyStudentAbsence::create(
            [
                'matricula_id' => $newRegistration->getKey(),
                'tipo_falta' => $newEvaluationRule->tipo_presenca,
            ]
        );
    }

    /**
     * Copia as faltas para a matrícula nova
     *
     * @param LegacyStudentAbsence $studentAbsence
     * @param LegacyRegistration   $oldRegistration
     * @param LegacyEvaluationRule $newEvaluationRule
     *
     * @throws MissingAbsenceType
     */
    private function createAbsence(
        LegacyStudentAbsence $studentAbsence,
        LegacyRegistration $oldRegistration,
        LegacyEvaluationRule $newEvaluationRule
    ) {
        if ($newEvaluationRule->tipo_presenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $this->copyDisciplineAbsence($studentAbsence, $oldRegistration);

            return;
        }

        if ($newEvaluationRule->tipo_presenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $this->copyGeneralAbsence($studentAbsence, $oldRegistration);

            return;
        }

        throw new MissingAbsenceType();
    }

    /**
     * Copia falta por componente
     *
     * @param LegacyStudentAbsence $studentAbsence
     * @param LegacyRegistration   $oldRegistration
     *
     * @return void
     */
    private function copyDisciplineAbsence(
        LegacyStudentAbsence $studentAbsence,
        LegacyRegistration $oldRegistration
    ) {
        $absences = $oldRegistration->studentAbsence->absences;

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
     * @param LegacyStudentAbsence $studentAbsence
     * @param LegacyRegistration   $oldRegistration
     */
    private function copyGeneralAbsence(
        LegacyStudentAbsence $studentAbsence,
        LegacyRegistration $oldRegistration
    ) {
        $absences = $oldRegistration->studentAbsence->absences;

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
