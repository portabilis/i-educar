<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEvaluationRule extends Model
{
    const PARALLEL_REMEDIAL_NONE = 0;
    const PARALLEL_REMEDIAL_PER_STAGE = 1;
    const PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE = 2;

    const PARALLEL_REMEDIAL_REPLACE_SCORE = 1;
    const PARALLEL_REMEDIAL_AVERAGE_SCORE = 2;
    const PARALLEL_REMEDIAL_SUM_SCORE = 3;

    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome', 'formula_media_id', 'formula_recuperacao_id', 'tipo_nota', 'tipo_progressao', 'tipo_presenca',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return bool
     */
    public function isAverageBetweenScoreAndRemedialCalculation()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_AVERAGE_SCORE;
    }

    /**
     * @return bool
     */
    public function isSumScoreCalculation()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_SUM_SCORE;
    }
}
