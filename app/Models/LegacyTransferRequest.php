<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyTransferRequest extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.transferencia_solicitacao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_transferencia_solicitacao';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_transferencia_tipo',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_cod_matricula_entrada',
        'ref_cod_matricula_saida',
        'observacao',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'data_transferencia',
        'ref_cod_escola_destino',
        'escola_destino_externa',
        'estado_escola_destino_externa',
        'municipio_escola_destino_externa',
    ];

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Relação com a matricula de saída.
     *
     * @return BelongsTo
     */
    public function oldRegistration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_saida');
    }

    /**
     * Relação com a matricula de entrada.
     *
     * @return BelongsTo
     */
    public function newRegistration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_entrada');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeUnattended($query)
    {
        return $query->whereNull('ref_cod_matricula_entrada');
    }
}
