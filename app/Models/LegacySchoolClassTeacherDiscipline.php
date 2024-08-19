<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $carga_horaria
 */
class LegacySchoolClassTeacherDiscipline extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'professor_turma_id',
        'componente_curricular_id',
    ];

    protected $table = 'modules.professor_turma_disciplina';

    public $timestamps = false;

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria,
        );
    }

    /**
     * @return BelongsTo<LegacySchoolClassTeacher, $this>
     */
    public function schoolClassTeacher(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClassTeacher::class, 'professor_turma_id');
    }

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'componente_curricular_id');
    }
}
