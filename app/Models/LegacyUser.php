<?php

namespace App\Models;

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\User as DefaultUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyUser extends DefaultUser
{
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
    protected $fillable = [
        'cod_usuario',
        'ref_cod_instituicao',
        'ref_funcionario_cad',
        'ref_cod_tipo_usuario',
        'data_cadastro',
        'ativo',
    ];

    protected $dispatchesEvents = [
        'updated' => UserUpdated::class,
        'deleted' => UserDeleted::class,
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_usuario,
        );
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario');
    }

    public function createdByEmployee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'ref_funcionario_cad');
    }

    public function deletedByEmployee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'ref_funcionario_exc');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }
}
