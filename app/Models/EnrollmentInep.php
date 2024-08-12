<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class EnrollmentInep extends Model
{
    protected $table = 'modules.educacenso_matricula';

    protected $fillable = [
        'matricula_turma_id',
        'matricula_inep',
    ];

    /**
     * @return BelongsTo<LegacyEnrollment, $this>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(LegacyEnrollment::class, 'matricula_turma_id');
    }
}
