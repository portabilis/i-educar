<?php

namespace App\Models;

use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $ref_cod_escola
 * @property int $ref_cod_serie
 */
class LegacySchoolGrade extends LegacyModel
{
    use HasLegacyDates;

    protected $table = 'pmieducar.escola_serie';

    protected $primaryKey = 'ref_cod_escola';

    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_cad',
        'anos_letivos',
        'hora_inicial',
        'hora_final',
        'hora_inicio_intervalo',
        'hora_fim_intervalo',
        'bloquear_enturmacao_sem_vagas',
        'bloquear_cadastro_turma_para_serie_com_vagas',
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
     *
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_cod_serie');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }
}
