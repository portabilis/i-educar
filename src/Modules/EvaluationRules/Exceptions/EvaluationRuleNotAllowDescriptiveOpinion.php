<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class EvaluationRuleNotAllowDescriptiveOpinion extends Exception
{
    /**
     * @param string $opinionType Tipo de parecer na mensagem, como "anual geral" ou "por etapa e componente"
     */
    public function __construct(
        private readonly int $schoolClassId,
        string $opinionType
    ) {
        parent::__construct(
            "A regra da turma {$this->schoolClassId} não permite lançamento de pareceres {$opinionType}.",
            Error::EVALUATION_RULE_NOT_ALLOW_DESCRIPTIVE_OPINION
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'school_class_id' => $this->schoolClassId,
        ];
    }
}
