<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $sigla
 */
class LegacyIssuingBody extends Model
{
    protected $table = 'cadastro.orgao_emissor_rg';

    protected $primaryKey = 'idorg_rg';

    protected $fillable = [
        'sigla',
        'descricao',
        'situacao',
        'codigo_educacenso',
    ];
}
