<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RegraAvaliacao_Model_TipoParecerDescritivo;

class LegacyStudentDescriptiveOpinion extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.parecer_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula_id',
        'parecer_descritivo',
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
    public function descriptiveOpinions()
    {
        if ($this->isByDiscipline()) {
            return $this->hasMany(LegacyDisciplineDescriptiveOpinion::class, 'parecer_aluno_id');
        }

        return $this->hasMany(LegacyGeneralDescriptiveOpinion::class, 'parecer_aluno_id');
    }

    /**
     * @return bool
     */
    public function isByDiscipline()
    {
        return $this->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
            || $this->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE;
    }

    /**
     * @return bool
     */
    public function isGeneral()
    {
        return $this->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
            || $this->parecer_descritivo == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL;
    }

    public function descriptiveOpinionByDiscipline()
    {
        return $this->hasMany(LegacyDisciplineDescriptiveOpinion::class, 'parecer_aluno_id');
    }

    public function generalDescriptiveOpinion()
    {
        return $this->hasMany(LegacyGeneralDescriptiveOpinion::class, 'parecer_aluno_id');
    }
}
