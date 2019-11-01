<?php

namespace App\Services;

use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
use RegraAvaliacao_Model_TipoParecerDescritivo;

class CopyDescriptiveOpinionService
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
    public function copyDescriptiveOpinions()
    {
        if (!$this->compatibleDescriptiveOpinionType()) {
            return;
        }

        $studentDescriptiveOpinion = $this->createStudentDescriptiveOpinion();
        $this->createDescriptiveOpinion($studentDescriptiveOpinion);
    }

    /**
     * Verifica se os tipos de pareceres descritivos das duas regras são iguais
     *
     * @return bool
     */
    private function compatibleDescriptiveOpinionType()
    {
        return $this->newEvaluationRule->parecer_descritivo == $this->oldEvaluationRule->parecer_descritivo;
    }

    /**
     * Cria o registro em parecer_aluno pra nova matrícula
     *
     * @return LegacyStudentDescriptiveOpinion
     */
    private function createStudentDescriptiveOpinion()
    {
        return LegacyStudentDescriptiveOpinion::create(
            [
                'matricula_id' => $this->newRegistration->getKey(),
                'parecer_descritivo' => $this->newEvaluationRule->parecer_descritivo,
            ]
        );
    }

    /**
     * Copia os pareceres para a matrícula nova
     *
     * @param LegacyStudentDescriptiveOpinion $studentDescriptiveOpinion
     * @throws MissingDescriptiveOpinionType
     */
    private function createDescriptiveOpinion($studentDescriptiveOpinion)
    {
        if ($this->newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
            return;
        }

        if ($this->newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
            || $this->newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
            $this->copyDisciplineDescriptiveOpinion($studentDescriptiveOpinion);
            return;
        }

        if ($this->newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
            || $this->newEvaluationRule->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL) {
            $this->copyGeneralDescriptiveOpinion($studentDescriptiveOpinion);
            return;
        }

        throw new MissingDescriptiveOpinionType();
    }

    /**
     * Copia parecer por componente
     *
     * @param $studentDescriptiveOpinion
     */
    private function copyDisciplineDescriptiveOpinion($studentDescriptiveOpinion)
    {
        $descriptiveOpinions = $this->oldRegistration->studentDescriptiveOpinion->descriptiveOpinions;

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
     * @param $studentDescriptiveOpinion
     */
    private function copyGeneralDescriptiveOpinion($studentDescriptiveOpinion)
    {
        $descriptiveOpinions = $this->oldRegistration->studentDescriptiveOpinion->descriptiveOpinions;

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
