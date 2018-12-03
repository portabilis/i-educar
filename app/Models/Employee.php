<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Funcionario.
 *
 * @package namespace App\Entities;
 */
class Employee extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'portal.funcionario';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_pessoa_fj';

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
