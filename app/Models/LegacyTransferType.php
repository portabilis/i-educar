<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyTransferType extends Model
{
    use HasFactory;
    use Ativo;

    CONST CREATED_AT = 'data_cadastro';
    CONST UPDATED_AT = null;

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
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
