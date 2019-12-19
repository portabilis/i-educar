<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * LegacyCourse
 *
 * @property string $name Nome do curso
 */
class LegacyCourse extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.curso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_curso';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'ref_cod_tipo_regime', 'ref_cod_nivel_ensino', 'ref_cod_tipo_ensino', 'nm_curso',
        'sgl_curso', 'qtd_etapas', 'carga_horaria', 'data_cadastro', 'ref_cod_instituicao', 'hora_falta', 'ativo',
        'modalidade_curso', 'padrao_ano_escolar', 'multi_seriado'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'padrao_ano_escolar' => 'boolean',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_curso;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_curso;
    }

    /**
     * @return bool
     */
    public function getIsStandardCalendarAttribute()
    {
        return $this->padrao_ano_escolar;
    }
}
