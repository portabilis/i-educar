<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyRemedialRule extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao_recuperacao';

    public const CREATED_AT = null;

    /**
     * @var array
     */
    protected $casts = [
        'nota_maxima' => 'float',
    ];

    protected $fillable = [
        'regra_avaliacao_id',
        'descricao',
        'etapas_recuperadas',
        'substitui_menor_nota',
        'media',
        'nota_maxima',
    ];

    public function getStages(): array
    {
        return explode(';', $this->etapas_recuperadas);
    }

    public function getLastStage(): int
    {
        return max($this->getStages());
    }

    public function evaluationRule(): BelongsTo
    {
        return $this->belongsTo(LegacyEvaluationRule::class, 'regra_avaliacao_id');
    }
}
