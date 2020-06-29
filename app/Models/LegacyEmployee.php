<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string $login
 * @property string $password
 * @property int    $department_id
 * @property int    $menu_type
 * @property string $email
 * @property string $remember_token
 * @property bool   $active
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

    protected $fillable = [
        'ref_cod_pessoa_fj',
        'matricula',
        'senha',
        'ativo',
        'force_reset_password',
        'email',
    ];

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

    /**
     * @return string
     */
    public function getRememberTokenAttribute()
    {
        return $this->status_token;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setRememberTokenAttribute($token)
    {
        $this->status_token = $token;
    }

    /**
     * @return boolean
     */
    public function getActiveAttribute()
    {
        return boolval($this->ativo);
    }
}
