<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Pessoa.
 *
 * @package namespace App\Entities;
 */
class LegacyConfiguration extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'pmieducar.configuracoes_gerais';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_instituicao';

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

}
