<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyPostalCodeStreet extends Model
{
    /**
     * @var string
     */
    protected $table = 'urbano.cep_logradouro';

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
        'nroini',
        'nrofin',
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
