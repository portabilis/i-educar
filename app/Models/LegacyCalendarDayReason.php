<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $tipo
 * @property string $nm_motivo
 * @property LegacySchool $school
 */
class LegacyCalendarDayReason extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public $table = 'pmieducar.calendario_dia_motivo';

    public $primaryKey = 'cod_calendario_dia_motivo';

    public $fillable = [
        'ref_cod_escola',
        'sigla',
        'descricao',
        'tipo',
        'nm_motivo',
    ];

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo == 'e' ? 'extra' : 'não-letivo',
        );
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nm_motivo,
        );
    }

    public function schoolName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->school->name,
        );
    }

    public function institutionName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->school->institution->name,
        );
    }
}
