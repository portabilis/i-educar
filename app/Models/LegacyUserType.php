<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property int    $id
 * @property int    $level
 * @property User[] $users
 */
class LegacyUserType extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_usuario';

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
     * @return int
     */
    public function getLevelAttribute()
    {
        return $this->nivel;
    }

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }
}
