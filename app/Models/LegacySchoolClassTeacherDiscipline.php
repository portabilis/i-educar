<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolClassTeacherDiscipline extends Model
{

    public $incrementing = false;
    public $primaryKey = null;
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

    public function schoolClassTeacher()
    {
        return $this->belongsTo(LegacySchoolClassTeacher::class, 'professor_turma_id');
    }
}
