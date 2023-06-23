<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyKnowledgeArea extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.area_conhecimento';

    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'instituicao_id');
    }

    public function disciplines(): HasMany
    {
        return $this->hasMany(LegacyDiscipline::class, 'area_conhecimento_id');
    }
}
