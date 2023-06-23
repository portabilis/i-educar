<?php

namespace App\Models;

use App\Models\Builders\LegacyAcademicYearStageBuilder;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAcademicYearStage extends LegacyModel
{
    use DateSerializer;

    /**
     * @var string
     */
    protected $table = 'pmieducar.ano_letivo_modulo';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacyAcademicYearStageBuilder::class;

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ano',
        'ref_ref_cod_escola',
        'sequencial',
        'ref_cod_modulo',
        'data_inicio',
        'data_fim',
        'dias_letivos',
        'escola_ano_letivo_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function stageType(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'ref_cod_modulo');
    }

    public function schoolAcademicYear(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolAcademicYear::class, 'ref_ref_cod_escola', 'ref_cod_escola');
    }
}
