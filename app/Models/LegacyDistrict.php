<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDistrict extends Model
{
    /**
     * @var string
     */
    protected $table = 'distrito';

    /**
     * @var string
     */
    protected $primaryKey = 'iddis';

    /**
     * @var array
     */
    protected $fillable = [
        'idmun',
        'geom',
        'iddis',
        'nome',
        'cod_ibge',
        'idpes_rev',
        'data_rev',
        'data_cad',
        'idpes_cad',
        'origem_gravacao',
        'operacao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
