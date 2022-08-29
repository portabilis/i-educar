<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

class LegacyEducationLevel extends Model
{
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.nivel_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_nivel_ensino';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_nivel',
        'descricao',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
}
