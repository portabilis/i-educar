<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class EvaluationRuleNotDefinedInLevel extends Exception
{
    /**
     * @var int
     */
    private $levelId;

    /**
     * @param int $levelId
     */
    public function __construct($levelId)
    {
        parent::__construct(
            "Regra de avaliação não informada na série para o ano letivo informado.",
            Error::EVALUATION_RULE_NOT_DEFINED_IN_LEVEL
        );

        $this->levelId = $levelId;
    }

    /**
     * @return array
     */
    public function getExtraInfo()
    {
        return [
            'level_code' => $this->levelId,
        ];
    }
}