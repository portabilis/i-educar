<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

class LegacyAbandonmentType extends Model
{
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.abandono_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_abandono_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_instituicao',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nome',
        'data_exclusao',
        'ativo'
    ];
}