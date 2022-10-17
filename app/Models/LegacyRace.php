<?php

namespace App\Models;

use App\Traits\HasLegacyDates;

class LegacyRace extends LegacyModel
{
    use HasLegacyDates;

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
        'raca_educacenso',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
