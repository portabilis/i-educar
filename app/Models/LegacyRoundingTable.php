<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return HasMany
     */
    public function roundingValues()
    {
        return $this->hasMany(LegacyValueRoundingTable::class, 'tabela_arredondamento_id', 'id');
    }
}
