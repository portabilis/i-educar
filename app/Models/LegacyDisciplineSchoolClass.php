<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LegacyDisciplineSchoolClass extends Pivot
{
    protected $table = 'modules.componente_curricular_turma';

    protected $primaryKey = 'componente_curricular_id';

    protected $fillable = [
        'componente_curricular_id',
        'ano_escolar_id',
        'escola_id',
        'turma_id',
        'carga_horaria',
        'docente_vinculado',
        'etapas_especificas',
        'etapas_utilizadas',
    ];

    public $timestamps = false;

    public $incrementing = false;
}
