<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyIssuingBody extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.orgao_emissor_rg';

    /**
     * @var string
     */
    protected $primaryKey = 'idorg_rg';

    /**
     * @var array
     */
    protected $fillable = [
        'sigla',
        'descricao',
        'situacao',
        'codigo_educacenso',
    ];
}
