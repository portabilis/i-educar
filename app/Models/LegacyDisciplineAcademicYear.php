<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LegacyDisciplineAcademicYear extends Pivot
{
    use HasFactory;

    protected $table = 'modules.componente_curricular_ano_escolar';

    protected $primaryKey = 'componente_curricular_id';

    protected $fillable = [
        'componente_curricular_id',
        'ano_escolar_id',
        'carga_horaria',
        'tipo_nota',
        'anos_letivos',
    ];

    public $timestamps = false;

    public $incrementing = false;
}
