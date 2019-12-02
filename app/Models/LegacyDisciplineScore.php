<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDisciplineScore extends Model
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
        'nota_aluno_id',
        'componente_curricular_id',
        'nota',
        'nota_arredondada',
        'etapa',
        'nota_recuperacao',
        'nota_original',
        'nota_recuperacao_especifica'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
