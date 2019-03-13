<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEnrollment extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_turma',
        'sequencial',
        'ref_usuario_cad',
        'data_cadastro',
        'data_enturmacao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
