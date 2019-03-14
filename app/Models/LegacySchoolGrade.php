<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolGrade extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_serie';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_cad',
        'data_cadastro',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
