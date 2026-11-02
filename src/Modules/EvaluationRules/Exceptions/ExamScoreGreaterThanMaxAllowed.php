<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class ExamScoreGreaterThanMaxAllowed extends Exception
{
    public function __construct(
        private readonly mixed $score,
        private readonly mixed $limit
    ) {
        parent::__construct(
            "A nota {$this->score} está acima da configurada para nota máxima para exame que é {$this->limit}.",
            Error::EXAM_SCORE_GREATER_THAN_MAX_ALLOWED
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
