<?php

namespace App\Models;

use App\Models\Builders\LegacySequenceGradeBuilder;
use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySequenceGrade extends LegacyModel
{
    use Ativo;

    protected $table = 'pmieducar.sequencia_serie';

    public const CREATED_AT = 'data_cadastro';

    protected string $builder = LegacySequenceGradeBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'ref_serie_origem',
        'ref_serie_destino',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'data_exclusao',
        'ativo',
    ];

    public function gradeOrigin(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_origem');
    }

    public function gradeDestiny(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_destino');
    }
}
