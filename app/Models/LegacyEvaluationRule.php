<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEvaluationRule extends Model
{
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
        'instituicao_id', 'nome', 'formula_media_id', 'tipo_nota', 'tipo_progressao', 'tipo_presenca',
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
        return $this->tipo_recuperacao_paralela == 1 && $this->tipo_calculo_recuperacao_paralela == 2;
    }

    /**
     * @return bool
     */
    public function isDoubleScoreCalculation()
    {
        return $this->tipo_recuperacao_paralela == 1 && $this->tipo_calculo_recuperacao_paralela == 3;
    }
}
