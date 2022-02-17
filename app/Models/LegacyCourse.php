<?php

namespace App\Models;

use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyCourse
 *
 * @property string        $name
 * @property LegacyGrade[] $grades
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

    /**
     * Relacionamento com as series
     *
     * @return HasMany
     */
    public function grades()
    {
        return $this->hasMany(LegacyGrade::class, 'ref_cod_curso');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIsEja($query)
    {
        return $query->where('modalidade_curso', ModalidadeCurso::EJA);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('curso.ativo', 1);
    }

    public function scopeRegistrationsActiveLastYear(Builder $query): Builder
    {
        return $query->join('pmieducar.matricula', 'curso.cod_curso', '=', 'matricula.ref_cod_curso')
            ->where('matricula.ano', date('Y') - 1)
            ->where('matricula.ativo', 1);
    }

    public function scopeRegistrationsActiveCurrentYear(Builder $query): Builder
    {
        return $query->join('pmieducar.matricula', 'curso.cod_curso', '=', 'matricula.ref_cod_curso')
            ->where('matricula.ano', date('Y'))
            ->where('matricula.ativo', 1);
    }

    public function scopeHasModality(Builder $query): Builder
    {
        return $query->where('modalidade_curso', '>', 0);
    }
}
