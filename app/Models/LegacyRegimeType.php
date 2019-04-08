<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRegimeType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_regime';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_regime';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'nm_tipo', 'data_cadastro', 'ativo', 'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
