<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.componente_curricular';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'area_conhecimento_id', 'nome', 'abreviatura', 'tipo_base',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
