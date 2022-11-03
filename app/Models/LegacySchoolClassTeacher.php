<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacySchoolClassTeacher extends Model
{
    public const CREATED_AT = null;

    protected $fillable = [
        'ano',
        'instituicao_id',
        'turma_id',
        'servidor_id',
        'funcao_exercida',
        'tipo_vinculo',
    ];

    /**
     * @var string
     */
    protected $table = 'modules.professor_turma';

    public function schoolClassTeacherDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacherDiscipline::class, 'professor_turma_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'turma_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'turno_id');
    }
}
