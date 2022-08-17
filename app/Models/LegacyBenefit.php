<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Ativo;

class LegacyBenefit extends Model
{
    use Ativo;
    use HasFactory;
    const CREATED_AT = 'data_cadastro';
    const UPDATED_AT = null;
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
