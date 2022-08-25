<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

class LegacyBenefit extends Model
{
    use Ativo;
    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;
    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno_beneficio';
    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno_beneficio';
    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_beneficio',
        'desc_beneficio',
        'data_cadastro',
        'data_exclusao',
        'ativo',
    ];
}
