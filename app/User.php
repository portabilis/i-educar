<?php

namespace App;

use App\Models\LegacyEmployee;
use App\Models\LegacyUserType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int            $id
 * @property string         $login
 * @property string         $password
 * @property LegacyUserType $type
 * @property LegacyEmployee $employee
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'pmieducar.usuario';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_usuario';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_usuario;
    }

    /**
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->employee->email;
    }

    /**
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->employee->login;
    }

    /**
     * @return string
     */
    public function getPasswordAttribute()
    {
        return $this->employee->password;
    }

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->employee->password = $password;
        $this->employee->save();
    }

    /**
     * @return string
     */
    public function getRememberTokenAttribute()
    {
        return $this->employee->remember_token;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setRememberTokenAttribute($token)
    {
        $this->employee->remember_token = $token;
        $this->employee->save();
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_usuario', 'ref_cod_pessoa_fj');
    }
}
