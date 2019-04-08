<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Fisica.
 *
 * @package namespace App\Entities;
 */
class LegacyIndividual extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'cadastro.fisica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes', 'data_cad', 'operacao', 'origem_gravacao', 'idsis_cad',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
