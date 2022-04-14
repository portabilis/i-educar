<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyConfiguration extends Model
{
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
