<?php

namespace App\Models;

use App\Models\Builders\LegacySequenceGradeBuilder;
use App\Traits\Ativo;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacySequenceGrade extends LegacyModel
{
    use Ativo;

    /** @use HasBuilder<LegacySequenceGradeBuilder> */
    use HasBuilder;

    protected $table = 'pmieducar.sequencia_serie';

    public const CREATED_AT = 'data_cadastro';

    protected static string $builder = LegacySequenceGradeBuilder::class;

    protected $fillable = [
        'ref_serie_origem',
        'ref_serie_destino',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'data_exclusao',
        'ativo',
    ];

    /**
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function gradeOrigin(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_origem');
    }

    /**
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function gradeDestiny(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_destino');
    }
}
