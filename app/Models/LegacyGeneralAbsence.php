<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $quantidade
 */
class LegacyGeneralAbsence extends Model
{
    protected $table = 'modules.falta_geral';

    protected $fillable = [
        'falta_aluno_id',
        'quantidade',
        'etapa',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyStudentAbsence, $this>
     */
    public function studentAbsence(): BelongsTo
    {
        return $this->belongsTo(LegacyStudentAbsence::class, 'falta_aluno_id');
    }

    public function absence(): int
    {
        return $this->quantidade;
    }
}
