<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyEvaluationRuleGradeYear extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao_serie_ano';

    /**
     * @var string
     */
    protected $primaryKey = 'serie_id';

    /**
     * @var array
     */
    protected $fillable = [
        'serie_id',
        'regra_avaliacao_id',
        'regra_avaliacao_diferenciada_id',
        'ano_letivo',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function evaluationRule()
    {
        return $this->belongsTo(LegacyEvaluationRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return BelongsTo
     */
    public function differentiatedEvaluationRule()
    {
        return $this->belongsTo(LegacyEvaluationRule::class, 'regra_avaliacao_diferenciada_id');
    }
}
