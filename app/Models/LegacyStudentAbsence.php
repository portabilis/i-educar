<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RegraAvaliacao_Model_TipoPresenca;

class LegacyStudentAbsence extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.falta_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula_id',
        'tipo_falta',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }

    public function absences(): HasMany
    {
        if ($this->isByDiscipline()) {
            return $this->hasMany(LegacyDisciplineAbsence::class, 'falta_aluno_id');
        }

        return $this->hasMany(LegacyGeneralAbsence::class, 'falta_aluno_id');
    }

    public function absencesByDiscipline(): HasMany
    {
        return $this->hasMany(LegacyDisciplineAbsence::class, 'falta_aluno_id');
    }

    public function generalAbsences(): HasMany
    {
        return $this->hasMany(LegacyGeneralAbsence::class, 'falta_aluno_id', 'id');
    }

    public function isByDiscipline(): bool
    {
        return $this->tipo_falta == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;
    }

    public function isGeneral(): bool
    {
        return $this->tipo_falta == RegraAvaliacao_Model_TipoPresenca::GERAL;
    }
}
