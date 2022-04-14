<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyTransferType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.transferencia_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_transferencia_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'desc_tipo',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];

    public $timestamps = false;
}
