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
     * @var array
     */
    protected $fillable = [
        'matricula_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }

    /**
     * @return HasMany
     */
    public function scoreGeneral()
    {
        return $this->hasMany(LegacyGeneralScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function scoreByDiscipline()
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function averageByDiscipline()
    {
        return $this->hasMany(LegacyDisciplineScoreAverage::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function averageGeneral()
    {
        return $this->hasMany(LegacyGeneralAverage::class, 'nota_aluno_id');
    }
}
