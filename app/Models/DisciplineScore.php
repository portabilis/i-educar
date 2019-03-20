<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineScore extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_componente_curricular';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'nota_aluno_id', 'componente_curricular_id', 'etapa',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
