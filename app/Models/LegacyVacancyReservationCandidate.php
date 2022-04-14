<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyVacancyReservationCandidate extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.candidato_reserva_vaga';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_candidato_reserva_vaga';

    /**
     * @var array
     */
    protected $fillable = [
        'ano_letivo',
        'data_solicitacao',
        'ref_cod_aluno',
        'ref_cod_serie',
        'ref_cod_turno',
        'ref_cod_pessoa_cad',
        'data_cad',
        'data_update',
        'ref_cod_matricula',
        'situacao',
        'data_situacao',
        'motivo',
        'ref_cod_escola',
        'quantidade_membros',
        'mae_fez_pre_natal',
        'membros_trabalham',
        'hora_solicitacao',
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
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }
}
