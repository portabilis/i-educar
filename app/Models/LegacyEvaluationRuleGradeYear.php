<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property LegacyEvaluationRule $evaluationRule
 * @property LegacyEvaluationRule $differentiatedEvaluationRule
 */
class LegacyEvaluationRuleGradeYear extends Model
{
    protected $table = 'modules.regra_avaliacao_serie_ano';

    protected $primaryKey = 'serie_id';

    protected $fillable = [
        'serie_id',
        'regra_avaliacao_id',
        'regra_avaliacao_diferenciada_id',
        'ano_letivo',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyEvaluationRule, $this>
     */
    public function evaluationRule(): BelongsTo
    {
        return $this->belongsTo(LegacyEvaluationRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return BelongsTo<LegacyEvaluationRule, $this>
     */
    public function differentiatedEvaluationRule(): BelongsTo
    {
        return $this->belongsTo(LegacyEvaluationRule::class, 'regra_avaliacao_diferenciada_id');
    }
}
