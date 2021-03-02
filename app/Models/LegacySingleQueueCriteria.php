<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySingleQueueCriteria extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.criterio_fila_unica';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_criterio_fila_unica';

    /**
     * @var bool
     */
    public $timestamps = false;
}
