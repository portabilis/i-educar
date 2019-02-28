<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Menu.
 *
 * @package namespace App\Entities;
 */
class LegacyMenu extends EloquentBaseModel implements Transformable
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
     * @return LegacySubmenu[]
     */
    public function submenus()
    {
        return $this->hasMany(LegacySubmenu::class, 'ref_cod_menu_menu', 'cod_menu_menu');
    }
}
