<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyUser extends Model
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

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_usuario;
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LegacyUserType::class, 'ref_cod_tipo_usuario', 'cod_tipo_usuario');
    }
}
