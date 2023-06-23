<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolClassStageBuilder;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolClassStage extends LegacyModel
{
    use DateSerializer;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_turma';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacySchoolClassStageBuilder::class;

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_turma',
        'ref_cod_modulo',
        'sequencial',
        'data_inicio',
        'data_fim',
        'dias_letivos',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function stageType(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'ref_cod_modulo');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma');
    }
}
