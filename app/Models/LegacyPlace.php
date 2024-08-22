<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyPlace extends Model
{
    protected $table = 'logradouro';

    protected $primaryKey = 'idlog';

    protected $fillable = [
        'idlog',
        'idtlog',
        'nome',
        'idmun',
        'geom',
        'ident_oficial',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
    ];

    public $timestamps = false;
}
