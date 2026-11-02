<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class ScoreLesserThanMinAllowed extends Exception
{
    public function __construct(
        private readonly mixed $score,
        private readonly mixed $limit
    ) {
        parent::__construct(
            "A nota {$this->score} está abaixo da configurada para nota mínima geral que é {$this->limit}.",
            Error::SCORE_LESSER_THAN_MIN_ALLOWED
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'score' => $this->score,
            'limit' => $this->limit,
        ];
    }
}
