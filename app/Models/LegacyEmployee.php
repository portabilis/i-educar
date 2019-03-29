<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string $login
 * @property string $password
 * @property int    $department_id
 * @property int    $menu_type
 */
class LegacyEmployee extends EloquentBaseModel implements Transformable
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
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->matricula;
    }

    /**
     * @return string
     */
    public function getPasswordAttribute()
    {
        return $this->senha;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->senha = $value;
    }

    /**
     * @return int
     */
    public function getDepartmentIdAttribute()
    {
        return $this->ref_cod_setor_new;
    }

    /**
     * @return int
     */
    public function getMenuTypeAttribute()
    {
        return $this->tipo_menu;
    }
}
