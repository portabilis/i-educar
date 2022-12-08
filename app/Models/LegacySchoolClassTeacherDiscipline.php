<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolClassTeacherDiscipline extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'professor_turma_id',
        'componente_curricular_id',
    ];

    /**
     * @var string
     */
    protected $table = 'modules.professor_turma_disciplina';

    /**
     * @var bool
     */
    public $timestamps = false;

    public function schoolClassTeacher(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClassTeacher::class, 'professor_turma_id');
    }
}
