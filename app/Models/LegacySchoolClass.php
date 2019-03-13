<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolClass extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_turma',
        'sgl_turma',
        'max_aluno',
        'data_cadastro',
        'ref_cod_turma_tipo',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
