<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStageType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_modulo';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_modulo',
        'ref_usuario_cad',
        'nm_tipo',
        'data_cadastro',
        'ref_cod_instituicao',
        'num_etapas',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
