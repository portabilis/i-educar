<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRoundingTable extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.tabela_arredondamento';

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome', 'tipo_nota'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
