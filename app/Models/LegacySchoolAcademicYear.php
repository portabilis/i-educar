<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolAcademicYear extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_ano_letivo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ano',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'andamento',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'turmas_por_ano',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
