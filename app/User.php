<?php

namespace App;

use App\Models\LegacyAccess;
use App\Models\LegacyEmployee;
use App\Models\LegacyPerson;
use App\Models\LegacyUserType;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int            $id
 * @property string         $name
 * @property string         $email
 * @property string         $role
 * @property string         $login
 * @property string         $password
 * @property string         $created_at
 * @property LegacyUserType $type
 * @property LegacyEmployee $employee
 */
class User extends Authenticatable
{
    use HasApiTokens;
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

    public $timestamps = false;

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
    public function getNameAttribute()
    {
        return $this->person->name;
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
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->type->name;
    }

    /**
     * @return string
     */
    public function getCreatedAtAttribute()
    {
        return $this->data_cadastro;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->type->level;
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
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_usuario');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type->level === LegacyUserType::LEVEL_ADMIN;
    }

    /**
     * @return bool
     */
    public function isSchooling()
    {
        return $this->type->level === LegacyUserType::LEVEL_SCHOOLING;
    }

    /**
     * @return bool
     */
    public function isInstitutional()
    {
        return $this->type->level === LegacyUserType::LEVEL_INSTITUTIONAL;
    }

    /**
     * @return bool
     */
    public function isLibrary()
    {
        return $this->type->level === LegacyUserType::LEVEL_LIBRARY;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return boolval($this->employee->ativo && $this->ativo);
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        if (empty($date = $this->employee->data_expiracao)) {
            return false;
        }

        return now()->isAfter($date);
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(LegacyEmployee::class, 'cod_usuario', 'ref_cod_pessoa_fj');
    }

    /**
     * @return BelongsTo
     */
    public function access()
    {
        return $this->belongsTo(LegacyAccess::class, 'cod_usuario', 'cod_pessoa');
    }

    /**
     * @return BelongsToMany
     */
    public function processes()
    {
        return $this->belongsToMany(
            Menu::class,
            'pmieducar.menu_tipo_usuario',
            'ref_cod_tipo_usuario',
            'menu_id',
            'ref_cod_tipo_usuario',
            'id'
        )->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * @return BelongsToMany
     */
    public function menu()
    {
        return $this->processes()
            ->wherePivot('visualiza', 1)
            ->withPivot(['visualiza', 'cadastra', 'exclui']);
    }

    /**
     * @return BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(
            School::class,
            'pmieducar.escola_usuario',
            'ref_cod_usuario',
            'ref_cod_escola',
            'cod_usuario',
            'cod_escola'
        );
    }

    public function getCreatedAtCustom(): ?Carbon
    {
        return Carbon::createFromTimestamp((new \DateTime($this->getCreatedAtAttribute()))->getTimestamp());
    }

    public function getEnabledUserDate(): ?Carbon
    {
        if ($this->employee) {
            return $this->employee->getEnabledUserDate();
        }

        return null;
    }

    public function getPasswordUpdatedDate(): ?Carbon
    {
        if ($this->employee) {
            return $this->employee->getPasswordUpdatedDate();
        }

        return null;
    }

    public function getLastAccessDate(): Carbon
    {
        $legacyAccess = $this->access()
            ->orderBy('data_hora', 'DESC')
            ->first();

        if (!$legacyAccess) {
            return $this->getCreatedAtCustom() ?? Carbon::now();
        }

        return $legacyAccess->data_hora;
    }

    public function getDaysSinceLastAccessOrEnabledUserDate(): int
    {
        $daysGone = 0;
        $lastAccessDate = $this->getLastAccessDate();

        if ($this->getEnabledUserDate() &&
            $this->getEnabledUserDate()->gt($lastAccessDate)) {
            $lastAccessDate = $this->getEnabledUserDate();
        }

        $currentDate = Carbon::now();
        if ($currentDate->gt($lastAccessDate)) {
            $daysGone = $currentDate->diffInDays($lastAccessDate);
        }

        return $daysGone;
    }

    public function getDaysSinceLastPasswordUpdated(): int
    {
        $daysGone = 0;
        $lastPasswordUpdatedDate = $this->getPasswordUpdatedDate();

        $currentDate = Carbon::now();
        if ($currentDate->gt($lastPasswordUpdatedDate)) {
            $daysGone = $currentDate->diffInDays($lastPasswordUpdatedDate);
        }

        return $daysGone;
    }

    public function getActiveUsersNotAdmin()
    {
        return $this->query()
            ->join('portal.funcionario', 'usuario.cod_usuario', '=', 'funcionario.ref_cod_pessoa_fj')
            ->where('funcionario.ativo', 1)
            ->where('ref_cod_tipo_usuario', '<>', LegacyUserType::LEVEL_ADMIN)
            ->get();
    }

    public function disable()
    {
        $this->employee->data_expiracao = now();
        $this->employee->ativo = 0;
        $this->employee->save();
        $this->ativo = 0;
        $this->save();
    }
}
