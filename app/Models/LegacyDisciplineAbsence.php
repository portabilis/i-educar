<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $quantidade
 */
class LegacyDisciplineAbsence extends Model
{
    protected $table = 'modules.falta_componente_curricular';

    protected $fillable = [
        'falta_aluno_id',
        'componente_curricular_id',
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

    public function absence(bool $isGeneralAbsence = false): int
    {
        return $isGeneralAbsence ? 0 : $this->quantidade;
    }
}
