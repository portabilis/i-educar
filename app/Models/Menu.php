<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Menu.
 *
 * @package namespace App\Entities;
 */
class Menu extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'portal.menu_menu';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_menu_menu';

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
     * @return Submenu[]
     */
    public function submenus()
    {
        return $this->hasMany(Submenu::class, 'ref_cod_menu_menu', 'cod_menu_menu');
    }
}
