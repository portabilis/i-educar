<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
