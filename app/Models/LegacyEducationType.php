<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEducationType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'nm_tipo', 'data_cadastro', 'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
