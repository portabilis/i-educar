<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyBenefit extends Model
{
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
