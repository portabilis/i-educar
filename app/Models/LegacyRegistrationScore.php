<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyRegistrationScore extends Model
{
    protected $table = 'modules.nota_aluno';

    protected $fillable = [
        'matricula_id',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }

    /**
     * @return HasMany<LegacyGeneralAverage, $this>
     */
    public function generalAverages(): HasMany
    {
        return $this->hasMany(LegacyGeneralAverage::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyDisciplineScoreAverage, $this>
     */
    public function disciplineScoreAverages(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScoreAverage::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyDisciplineScore, $this>
     */
    public function disciplineScores(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }
}
