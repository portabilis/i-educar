<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDisciplineDependence extends Model
{
    public const CREATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.disciplina_dependencia';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_disciplina_dependencia';

    public $fillable = [
        'ref_cod_matricula',
        'ref_cod_disciplina',
        'ref_cod_escola',
        'ref_cod_serie',
        'observacao',
        'cod_disciplina_dependencia',
    ];
}
