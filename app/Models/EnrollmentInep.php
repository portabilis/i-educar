<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentInep extends Model
{
    protected $table = 'modules.educacenso_matricula';

    protected $fillable = [
        'matricula_turma_id',
        'matricula_inep',
    ];

    public function enrollment()
    {
        return $this->belongsTo(LegacyEnrollment::class, 'matricula_turma_id');
    }
}
