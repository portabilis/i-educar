<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'pmieducar.usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_usuario';

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

    /**
     * @return UserType
     */
    public function type()
    {
        return $this->belongsTo(UserType::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }
}
