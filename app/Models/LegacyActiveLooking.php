<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LegacyActiveLooking
 *
 * @property LegacyRegistration $registration
 * @property int                id
 */
class LegacyActiveLooking extends LegacyModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'pmieducar.busca_ativa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'data_inicio',
        'data_fim',
        'observacoes',
        'resultado_busca_ativa',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public array $legacy = [
        'registration_id' => 'ref_cod_matricula',
        'start' => 'data_inicio',
        'end' => 'data_fim',
        'obs' => 'observacoes',
        'result' => 'resultado_busca_ativa',
    ];

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    public function getStartDate()
    {
        return $this->data_inicio ? $this->data_inicio->format('Y-m-d') : null;
    }

    public function getEndDate()
    {
        return $this->data_fim ? $this->data_fim->format('Y-m-d') : null;
    }
}
