<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGradeSequence extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = null;

    protected $table = 'pmieducar.sequencia_serie';

    protected $primaryKey = 'ref_serie_origem';

    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_serie_origem',
        'ref_serie_destino',
        'ativo',
        'data_cadastro',
        'data_exclusao',
    ];

    public function from(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_origem');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_serie_destino');
    }
}
