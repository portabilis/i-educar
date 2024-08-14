<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyKnowledgeArea extends Model
{
    protected $table = 'modules.area_conhecimento';

    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'instituicao_id', 'nome',
    ];

    /**
     * @return BelongsTo<LegacyInstitution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'instituicao_id');
    }

    /**
     * @return HasMany<LegacyDiscipline, $this>
     */
    public function disciplines(): HasMany
    {
        return $this->hasMany(LegacyDiscipline::class, 'area_conhecimento_id');
    }
}
