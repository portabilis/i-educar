<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolClassTeacher extends Model
{
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

    /**
     * @var bool
     */
    public $timestamps = false;

    public function schoolClassTeacherDisciplines()
    {
        return $this->hasMany(LegacySchoolClassTeacherDiscipline::class, 'professor_turma_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'turma_id');
    }
}
