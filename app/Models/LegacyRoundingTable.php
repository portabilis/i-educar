<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyRoundingTable extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.tabela_arredondamento';

    public const CREATED_AT = null;

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id',
        'nome',
        'tipo_nota',
    ];

    public function roundingValues(): HasMany
    {
        return $this->hasMany(LegacyValueRoundingTable::class, 'tabela_arredondamento_id', 'id');
    }

    public function evaluationRules(): HasMany
    {
        return $this->hasMany(LegacyEvaluationRule::class, 'tabela_arredondamento_id');
    }
}
