<?php

namespace App\Services;

use App\Contracts\CopyRegistrationData;
use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
use RegraAvaliacao_Model_TipoParecerDescritivo;

class CopyDescriptiveOpinionService implements CopyRegistrationData
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
     * Copia notas descritivas de uma matrícula pra outra
     *
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     *
     * @throws MissingDescriptiveOpinionType
     */
    public function copy(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        $newEvaluationRule = $this->service->getEvaluationRule($newRegistration);
        $oldEvaluationRule = $this->service->getEvaluationRule($oldRegistration);

        if (!$this->compatibleDescriptiveOpinionType($newEvaluationRule, $oldEvaluationRule)) {
            return;
        }

        $studentDescriptiveOpinion = $this->createStudentDescriptiveOpinion(
            $newRegistration,
            $newEvaluationRule
        );

        $this->createDescriptiveOpinion(
            $studentDescriptiveOpinion,
            $newEvaluationRule,
            $oldRegistration
        );
    }

    /**
     * Verifica se os tipos de pareceres descritivos das duas regras são iguais
     *
     * @param LegacyEvaluationRule $newEvaluationRule
     * @param LegacyEvaluationRule $oldEvaluationRule
     *
     * @return bool
     */
    private function compatibleDescriptiveOpinionType(
        LegacyEvaluationRule $newEvaluationRule,
        LegacyEvaluationRule $oldEvaluationRule
    ) {
        return $newEvaluationRule->parecer_descritivo == $oldEvaluationRule->parecer_descritivo;
    }

    /**
     * Cria o registro em parecer_aluno pra nova matrícula
     *
     * @param LegacyRegistration   $newRegistration
     * @param LegacyEvaluationRule $newEvaluationRule
     *
     * @return LegacyStudentDescriptiveOpinion
     */
    private function createStudentDescriptiveOpinion(
        LegacyRegistration $newRegistration,
        LegacyEvaluationRule $newEvaluationRule
    ) {
        return LegacyStudentDescriptiveOpinion::create(
            [
                'matricula_id' => $newRegistration->getKey(),
                'parecer_descritivo' => $newEvaluationRule->parecer_descritivo,
            ]
        );
    }

    /**
     * Copia os pareceres para a matrícula nova
     *
     * @param LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion
     * @param LegacyEvaluationRule            $newEvaluationRule
     * @param LegacyRegistration              $oldRegistration
     *
     * @throws MissingDescriptiveOpinionType
     */
    private function createDescriptiveOpinion(
        LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion,
        LegacyEvaluationRule $newEvaluationRule,
        LegacyRegistration $oldRegistration
    ) {
        if ($newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
            return;
        }

        if ($newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
            || $newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            $this->copyDisciplineDescriptiveOpinion($studentDescriptiveOpinion, $oldRegistration);

            return;
        }

        if ($newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
            || $newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
            $this->copyGeneralDescriptiveOpinion($studentDescriptiveOpinion, $oldRegistration);

            return;
        }

        throw new MissingDescriptiveOpinionType();
    }

    /**
     * Copia parecer por componente
     *
     * @param LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion
     * @param LegacyRegistration              $oldRegistration
     */
    private function copyDisciplineDescriptiveOpinion(
        LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion,
        LegacyRegistration $oldRegistration
    ) {
        $descriptiveOpinions = $oldRegistration->studentDescriptiveOpinion->descriptiveOpinions;

        foreach ($descriptiveOpinions as $descriptiveOpinion) {
            LegacyDisciplineDescriptiveOpinion::create(
                [
                    'parecer_aluno_id' => $studentDescriptiveOpinion->getKey(),
                    'parecer' => $descriptiveOpinion->parecer,
                    'componente_curricular_id' => $descriptiveOpinion->componente_curricular_id,
                    'etapa' => $descriptiveOpinion->etapa,
                ]
            );
        }
    }

    /**
     * Copia parecer geral
     *
     * @param LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion
     * @param LegacyRegistration              $oldRegistration
     */
    private function copyGeneralDescriptiveOpinion(
        LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion,
        LegacyRegistration $oldRegistration
    ) {
        $descriptiveOpinions = $oldRegistration->studentDescriptiveOpinion->descriptiveOpinions;

        foreach ($descriptiveOpinions as $descriptiveOpinion) {
            LegacyGeneralDescriptiveOpinion::create(
                [
                    'parecer_aluno_id' => $studentDescriptiveOpinion->getKey(),
                    'parecer' => $descriptiveOpinion->parecer,
                    'etapa' => $descriptiveOpinion->etapa,
                ]
            );
        }
    }
}
