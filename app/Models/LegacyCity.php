<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyCity extends Model
{
    /**
     * @var string
     */
    protected $table = 'municipio';

    /**
     * @var string
     */
    protected $primaryKey = 'idmun';

    /**
     * @var array
     */
    protected $fillable = [
        'idmun',
        'nome',
        'sigla_uf',
        'area_km2',
        'idmreg',
        'idasmun',
        'cod_ibge',
        'geom',
        'tipo',
        'idmun_pai',
        'idpes_rev',
        'idpes_cad',
        'data_rev',
        'data_cad',
        'origem_gravacao',
        'operacao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
