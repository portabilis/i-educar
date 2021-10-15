<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyValueRoundingTable extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.tabela_arredondamento_valor';

    /**
     * @var array
     */
    protected $fillable = [
        'tabela_arredondamento_id',
        'nome',
        'descricao',
        'valor_minimo',
        'valor_maximo',
        'casa_decimal_exata',
        'acao',
        'observacao'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'valor_minimo' => 'float',
        'valor_maximo' => 'float',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
