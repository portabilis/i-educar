<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LegacySingleQueueCandidate extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.candidato_fila_unica';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_candidato_fila_unica';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_candidato_fila_unica',
        'ref_cod_aluno',
        'ref_cod_serie',
        'ref_cod_turno',
        'ref_cod_pessoa_cad',
        'ref_cod_pessoa_exc',
        'ref_cod_matricula',
        'ano_letivo',
        'data_cadastro',
        'data_exclusao',
        'data_solicitacao',
        'hora_solicitacao',
        'horario_inicial',
        'horario_final',
        'situacao',
        'via_judicial',
        'via_judicial_doc',
        'ativo',
        'motivo',
        'data_situacao',
        'historico',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->data_situacao = now()->format('Y-m-d');
            $model->data_solicitacao = now()->format('Y-m-d');
            $model->hora_solicitacao = now()->format('H:i:s');
        });
    }

    /**
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_cod_serie');
    }

    /**
     * @return BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(LegacyPeriod::class, 'ref_cod_turno');
    }

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * @return HasOne
     */
    public function school()
    {
        $hasOne = $this->hasOne(LegacySchoolSingleQueueCandidate::class, 'ref_cod_candidato_fila_unica');

        $hasOne->getQuery()->orderBy('sequencial');

        return $hasOne;
    }

    /**
     * @return HasMany
     */
    public function schools()
    {
        return $this->hasMany(LegacySchoolSingleQueueCandidate::class, 'ref_cod_candidato_fila_unica');
    }

    /**
     * @return BelongsToMany
     */
    public function criteria()
    {
        return $this->belongsToMany(
            LegacySingleQueueCriteria::class,
            'pmieducar.criterio_candidato_fila_unica',
            'ref_cod_candidato_fila_unica',
            'ref_cod_criterio_fila_unica',
            'cod_candidato_fila_unica',
            'cod_criterio_fila_unica',
        );
    }
}
