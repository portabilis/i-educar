<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Pessoa.
 *
 * @package namespace App\Entities;
 */
class LegacyCompany extends EloquentBaseModel
{
    /**
     * @var string
     */
    protected $table = 'cadastro.juridica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
