<?php

namespace App\Models;

use App\Models\Builders\LegacyTransferRequestBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyTransferRequest extends LegacyModel
{
    /** @use HasBuilder<LegacyTransferRequestBuilder> */
    use HasBuilder;

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

    protected static string $builder = LegacyTransferRequestBuilder::class;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_transferencia' => 'date',
    ];

    /**
     * @var array<int, string>
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
     *
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function oldRegistration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_saida');
    }

    /**
     * Relação com a matricula de entrada.
     *
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function newRegistration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula_entrada');
    }

    /**
     * @return BelongsTo<LegacyTransferType, $this>
     */
    public function transferType(): BelongsTo
    {
        return $this->belongsTo(LegacyTransferType::class, 'ref_cod_transferencia_tipo');
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function destinationSchool(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola_destino');
    }
}
