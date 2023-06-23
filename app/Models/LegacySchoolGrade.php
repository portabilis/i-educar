<?php

namespace App\Models;

use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolGrade extends LegacyModel
{
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_serie';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_cad',
        'anos_letivos',
        'hora_inicial',
        'hora_final',
        'hora_inicio_intervalo',
        'hora_fim_intervalo',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected function schoolId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_escola
        );
    }

    protected function gradeId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_serie
        );
    }

    /**
     * Relacionamento com a série.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_cod_serie');
    }
}
