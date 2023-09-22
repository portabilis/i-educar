<?php

namespace App\Models;

use App\Models\Builders\LegacyTransferRequestBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyTransferRequest extends LegacyModel
{
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    /**
     * @var string
     */
    protected $table = 'pmieducar.transferencia_solicitacao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_transferencia_solicitacao';

    public string $builder = LegacyTransferRequestBuilder::class;

    protected $casts = [
        'data_transferencia' => 'date',
    ];

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
        'data_exclusao',
        'ativo',
        'data_transferencia',
        'ref_cod_escola_destino',
        'escola_destino_externa',
        'estado_escola_destino_externa',
        'municipio_escola_destino_externa',
    ];

    /**
     * Relação com a matricula de saída.
     */
    public function oldRegistration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_saida');
    }

    /**
     * Relação com a matricula de entrada.
     */
    public function newRegistration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_entrada');
    }

    public function transferType(): BelongsTo
    {
        return $this->belongsTo(LegacyTransferType::class, 'ref_cod_transferencia_tipo');
    }

    public function destinationSchool(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola_destino');
    }
}
