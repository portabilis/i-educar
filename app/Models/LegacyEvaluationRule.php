<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    protected $casts = [
        'media_recuperacao_paralela' => 'float',
    ];

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
     * @return HasMany
     */
    public function remedialRules()
    {
        return $this->hasMany(LegacyRemedialRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return HasOne
     */
    public function roundingTable()
    {
        return $this->hasOne(LegacyRoundingTable::class, 'id', 'tabela_arredondamento_id');
    }

    /**
     * @return HasOne
     */
    public function conceptualRoundingTable()
    {
        return $this->hasOne(LegacyRoundingTable::class, 'id', 'tabela_arredondamento_id_conceitual');
    }

    /**
     * @return HasOne
     */
    public function deficiencyEvaluationRule()
    {
        return $this->hasOne(LegacyEvaluationRule::class, 'id', 'regra_diferenciada_id');
    }

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
    public function isSpecificRetake ()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE;
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
