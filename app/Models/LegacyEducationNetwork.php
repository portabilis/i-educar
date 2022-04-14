<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEducationNetwork extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_rede_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola_rede_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola_rede_ensino',
        'ref_usuario_cad',
        'nm_rede',
        'data_cadastro',
        'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
