<?php

namespace App\Models;

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\User as DefaultUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $cod_usuario
 */
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
     * @var array<int, string>
     */
    protected $fillable = [
        'cod_usuario',
        'ref_cod_instituicao',
        'ref_funcionario_cad',
        'ref_cod_tipo_usuario',
        'data_cadastro',
        'ativo',
    ];

    /**
     * @var array<string, string>
     */
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

    /**
     * @return BelongsTo<LegacyUserType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario');
    }

    /**
     * @return BelongsTo<LegacyEmployee, $this>
     */
    public function createdByEmployee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'ref_funcionario_cad');
    }

    /**
     * @return BelongsTo<LegacyEmployee, $this>
     */
    public function deletedByEmployee(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployee::class, 'ref_funcionario_exc');
    }

    /**
     * @return BelongsTo<LegacyInstitution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }
}
