<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyRoundingTable extends Model
{
    protected $table = 'modules.tabela_arredondamento';

    public const CREATED_AT = null;

    protected $fillable = [
        'instituicao_id',
        'nome',
        'tipo_nota',
    ];

    /**
     * @return HasMany<LegacyValueRoundingTable, $this>
     */
    public function roundingValues(): HasMany
    {
        return $this->hasMany(LegacyValueRoundingTable::class, 'tabela_arredondamento_id', 'id');
    }

    /**
     * @return HasMany<LegacyEvaluationRule, $this>
     */
    public function evaluationRules(): HasMany
    {
        return $this->hasMany(LegacyEvaluationRule::class, 'tabela_arredondamento_id');
    }
}
