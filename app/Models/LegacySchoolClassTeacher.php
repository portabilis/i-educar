<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolClassTeacher extends Model
{
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
