<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyStudentScore extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_aluno';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'matricula_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }

    /**
     * @return HasMany<LegacyGeneralScore, $this>
     */
    public function scoreGeneral(): HasMany
    {
        return $this->hasMany(LegacyGeneralScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyDisciplineScore, $this>
     */
    public function scoreByDiscipline(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyDisciplineScoreAverage, $this>
     */
    public function averageByDiscipline(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScoreAverage::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyGeneralAverage, $this>
     */
    public function averageGeneral(): HasMany
    {
        return $this->hasMany(LegacyGeneralAverage::class, 'nota_aluno_id');
    }
}
