<?php

namespace App\Models;

use App\Models\Builders\LegacyCourseBuilder;
use App\Traits\LegacyAttribute;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyCourse
 *
 * @property string        $name
 * @property LegacyGrade[] $grades
 */
class LegacyCourse extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.curso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_curso';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyCourseBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var array
     */
    public $legacy = [
        'id' => 'cod_curso',
        'name' => 'nm_curso',
        'is_standard_calendar' => 'padrao_ano_escolar',
        'steps' => 'qtd_etapas',
        'description' => 'descricao'
    ];

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
    public function getDescriptionAttribute()
    {
        return $this->descricao;
    }

    /**
     * @return int
     */
    public function getStepsAttribute()
    {
        return$this->qtd_etapas;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        if (empty($this->description)) {
            return $this->nm_curso;
        }

        return $this->nm_curso . ' (' . $this->description . ')';
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
     * Relaciona com  as escolas
     *
     * @return BelongsToMany
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class,'escola_curso','ref_cod_curso','ref_cod_escola')->wherePivot('ativo',1);
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

    /**
     * Filtra por Instituição
     *
     * @param Builder $query
     * @param int $institution
     * @return void
     */
    public function scopeWhereInstitution(Builder $query, int $institution): void
    {
        $query->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por Escola
     *
     * @param Builder $query
     * @param int $school
     * @return void
     */
    public function scopeWhereSchool(Builder $query, int $school): void
    {
        $query->whereHas('schools', function ($q) use ($school) {
            $q->where('cod_escola', $school);
        });
    }

    /**
     * Filtra por Padrão Ano Escolar
     *
     * @param Builder $query
     * @param bool $condition
     * @return void
     */
    public function scopeWhereNotIsStandardCalendar(Builder $query, bool $condition = true): void
    {
        $query->when($condition,fn($q) => $q->where('padrao_ano_escolar',0));
    }

    /**
     * Filtra o Curso
     *
     * @param Builder $query
     * @param int $course
     * @return void
     */
    public function scopeWhereCourse(Builder $query, int $course ): void
    {
        $query->where('cod_curso',$course);
    }

    /**
     * Ordena por nome
     *
     * @param Builder $query
     * @param string $direction
     * @return void
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('nm_curso',$direction);
    }
}
