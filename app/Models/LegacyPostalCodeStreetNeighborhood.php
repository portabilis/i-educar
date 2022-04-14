<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyPostalCodeStreetNeighborhood extends Model
{
    /**
     * @var string
     */
    protected $table = 'urbano.cep_logradouro_bairro';

    /**
     * @var string
     */
    protected $primaryKey = 'cep';

    /**
     * @var array
     */
    protected $fillable = [
        'cep',
        'idlog',
        'idbai',
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
