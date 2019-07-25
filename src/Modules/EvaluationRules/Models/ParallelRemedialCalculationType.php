<?php

namespace iEducar\Modules\EvaluationRules\Models;

class ParallelRemedialCalculationType
{
    const REPLACE_SCORE = 1;
    const AVERAGE_SCORE = 2;
    const SUM_SCORE = 3;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::REPLACE_SCORE => 'Substituir nota',
            self::AVERAGE_SCORE => 'Média entre nota e recuperação',
            self::SUM_SCORE => 'Soma entre nota e recuperação',
        ];
    }
}
