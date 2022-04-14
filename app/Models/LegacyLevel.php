<?php

namespace App\Models;

/**
 * @deprecated
 * @see LegacyGrade
 */
class LegacyLevel extends LegacyGrade
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
        'nm_serie', 'ref_usuario_cad', 'ref_cod_curso', 'etapa_curso', 'carga_horaria', 'data_cadastro', 'concluinte',
        'dias_letivos', 'ativo', 'intervalo'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_serie;
    }

    public function getCourseIdAttribute()
    {
        return $this->ref_cod_curso;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function evaluationRules()
    {
        return $this->belongsToMany(
            LegacyEvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }
}
