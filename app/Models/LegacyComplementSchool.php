<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string $name
 */
class LegacyComplementSchool extends EloquentBaseModel
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_complemento';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'email',
        'nm_escola',
        'data_cadastro',
        'ativo',
        'cep',
        'municipio'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacyPersonAddress::class, 'ref_cod_escola', 'cod_escola');
    }
}
