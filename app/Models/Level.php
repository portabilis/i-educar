<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
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
     * @var array
     */
    protected $fillable = [
        'nm_serie', 'ref_usuario_cad', 'ref_cod_curso', 'etapa_curso', 'carga_horaria', 'data_cadastro',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function evaluationRules()
    {
        return $this->belongsToMany(
            EvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }
}
