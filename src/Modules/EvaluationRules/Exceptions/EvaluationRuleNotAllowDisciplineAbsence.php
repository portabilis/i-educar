<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class EvaluationRuleNotAllowDisciplineAbsence extends Exception
{
    public function __construct(
        private readonly int $schoolClassId
    ) {
        parent::__construct(
            "A regra da turma {$this->schoolClassId} não permite lançamento de faltas por componente.",
            Error::SCHOOL_CLASS_DOESNT_ALOW_FREQUENCY_BY_DISCIPLINE
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'school_class_id' => $this->schoolClassId,
        ];
    }
}
