<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.serie';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_serie';

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function regrasAvaliacao()
    {
        return $this->belongsToMany(
            RegraAvaliacao::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }
}
