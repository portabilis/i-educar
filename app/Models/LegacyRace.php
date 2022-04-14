<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRace extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.raca';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_raca';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes_cad',
        'nm_raca',
        'data_cadastro',
        'raca_educacenso',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
