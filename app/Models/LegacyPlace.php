<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyPlace extends Model
{
    /**
     * @var string
     */
    protected $table = 'logradouro';

    /**
     * @var string
     */
    protected $primaryKey = 'idlog';

    /**
     * @var array
     */
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

    /**
     * @var bool
     */
    public $timestamps = false;
}
