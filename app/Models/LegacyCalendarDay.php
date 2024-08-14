<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacyCalendarDay extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public $table = 'pmieducar.calendario_dia';

    public $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_calendario_ano_letivo',
        'mes',
        'dia',
        'ref_cod_calendario_dia_motivo',
        'descricao',
    ];

    /**
     * @return BelongsTo<LegacyCalendarDayReason, $this>
     */
    public function calendarDayReason(): BelongsTo
    {
        return $this->belongsTo(LegacyCalendarDayReason::class, 'ref_cod_calendario_dia_motivo');
    }

    /**
     * @return BelongsTo<LegacyCalendarYear, $this>
     */
    public function calendarYear(): BelongsTo
    {
        return $this->belongsTo(LegacyCalendarYear::class, 'ref_cod_calendario_ano_letivo');
    }
}
