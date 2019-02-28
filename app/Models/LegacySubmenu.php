<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Submenu.
 *
 * @package namespace App\Entities;
 */
class LegacySubmenu extends EloquentBaseModel implements Transformable
{
    const SUPER_USER_MENU_ID = 0;

    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'portal.menu_submenu';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_menu_submenu';

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
     * @return LegacyMenu
     */
    public function menu()
    {
        return $this->belongsTo(LegacyMenu::class, 'ref_cod_menu_menu', 'cod_menu_menu');
    }

    /**
     * @return LegacyUserType[]
     */
    public function typeUsers()
    {
        return $this->belongsToMany(
            LegacyUserType::class,
            'pmieducar.menu_tipo_usuario',
            'ref_cod_menu_submenu',
            'ref_cod_tipo_usuario'
        );
    }
}
