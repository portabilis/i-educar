<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolClassStageBuilder;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacySchoolClassStage extends LegacyModel
{
    use DateSerializer;

    /** @use HasBuilder<LegacySchoolClassStageBuilder> */
    use HasBuilder;

    protected $table = 'pmieducar.turma_modulo';

    protected $primaryKey = 'ref_cod_turma';

    protected static string $builder = LegacySchoolClassStageBuilder::class;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

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

    /**
     * @return BelongsTo<LegacyStageType, $this>
     */
    public function stageType(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'ref_cod_modulo');
    }

    /**
     * @return BelongsTo<LegacySchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma');
    }
}
