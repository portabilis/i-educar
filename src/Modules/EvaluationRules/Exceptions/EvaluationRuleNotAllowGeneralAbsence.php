<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class EvaluationRuleNotAllowGeneralAbsence extends Exception
{
    /**
     * @var int
     */
    private $schoolClassId;

    /**
     * @param int $schoolClassId
     */
    public function __construct($schoolClassId)
    {
        parent::__construct(
            "A regra da turma {$schoolClassId} não permite lançamento de faltas geral.",
            Error::EVALUATION_RULE_NOT_ALLOW_GENERAL_ABSENCE
        );

        $this->schoolClassId = $schoolClassId;
    }

    /**
     * @return array
     */
    public function getExtraInfo()
    {
        return [
            'school_class_id' => $this->schoolClassId
        ];
    }
}