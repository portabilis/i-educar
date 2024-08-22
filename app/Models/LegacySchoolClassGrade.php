<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacySchoolClassGrade extends Model
{
    protected $table = 'pmieducar.turma_serie';

    protected $fillable = [
        'escola_id',
        'serie_id',
        'turma_id',
        'boletim_id',
        'boletim_diferenciado_id',
    ];

    /**
     * @return BelongsTo<LegacySchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'turma_id');
    }

    /**
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'serie_id');
    }
}
