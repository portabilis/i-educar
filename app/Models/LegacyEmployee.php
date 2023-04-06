<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $login
 * @property string $password
 * @property int    $department_id
 * @property int    $menu_type
 * @property string $email
 * @property string $remember_token
 * @property bool   $active
 */
class LegacyEmployee extends Model
{
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

    protected $casts = [
        'data_reativa_conta' => 'date',
        'data_troca_senha' => 'date',
    ];

    protected function login(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->matricula,
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->senha,
            set: static fn ($value) => [
                'senha' => $value,
            ],
        );
    }

    protected function departmentId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_setor_new,
        );
    }

    protected function menuType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo_menu,
        );
    }

    protected function rememberToken(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_token,
            set: static fn ($value) => [
                'status_token' => $value,
            ],
        );
    }

    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ativo,
        );
    }

    public function getEnabledUserDate(): ?Carbon
    {
        return $this->data_reativa_conta;
    }

    public function getPasswordUpdatedDate(): ?Carbon
    {
        return $this->data_troca_senha;
    }
}
