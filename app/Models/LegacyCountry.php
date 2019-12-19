<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyCountry extends Model
{
    /**
     * @var string
     */
    protected $table = 'pais';

    /**
     * @var string
     */
    protected $primaryKey = 'idpais';

    /**
     * @var array
     */
    protected $fillable = [
        'idpais',
        'nome',
        'geom',
        'cod_ibge',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
