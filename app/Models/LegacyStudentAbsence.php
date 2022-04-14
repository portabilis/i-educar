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
    public function absences()
    {
        if ($this->isByDiscipline()) {
            return $this->hasMany(LegacyDisciplineAbsence::class, 'falta_aluno_id');
        }

        return $this->hasMany(LegacyGeneralAbsence::class, 'falta_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function absencesByDiscipline()
    {
        return $this->hasMany(LegacyDisciplineAbsence::class, 'falta_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function generalAbsences()
    {
        return $this->hasMany(LegacyGeneralAbsence::class, 'falta_aluno_id');
    }

    /**
     * @return bool
     */
    public function isByDiscipline()
    {
        return $this->tipo_falta == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;
    }

    /**
     * @return bool
     */
    public function isGeneral()
    {
        return $this->tipo_falta == RegraAvaliacao_Model_TipoPresenca::GERAL;
    }
}
