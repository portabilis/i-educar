<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

class LegacyEducationType extends Model
{
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

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
        'ref_usuario_cad',
        'ref_usuario_exc',
        'nm_tipo',
        'ativo',
        'ref_cod_instituicao',
        'atividade_complementar',
        'data_exclusao'
    ];
}
