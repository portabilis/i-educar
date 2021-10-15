<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolClassType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_tipo',
        'sgl_tipo',
        'data_cadastro',
        'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
