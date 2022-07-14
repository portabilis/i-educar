<?php

namespace App\Models;

use App\Models\Builders\LegacyGradeBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyGrade extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.serie';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_serie';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyGradeBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'cod_serie',
        'name' => 'nm_serie',
        'description' => 'descricao'
    ];

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
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_serie;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        if (empty($this->description)) {
            return $this->nm_serie;
        }

        return $this->nm_serie . ' (' . $this->description . ')';
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
    public function getCourseIdAttribute()
    {
        return $this->ref_cod_curso;
    }

    /**
     * Regras de avaliação
     *
     * @return BelongsToMany
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

    /**
     * Escolas
     *
     * @return BelongsToMany
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class,'escola_serie','ref_cod_serie','ref_cod_escola')->wherePivot('ativo',1);
    }

    /**
     * Relacionamento com o curso.
     *
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * Relacionamento com a turma.
     *
     * @return HasMany
     */
    public function schoolClass()
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_serie');
    }

    /**
     * Filtra por ativos
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('serie.ativo', 1);
    }

    /**
     * Filtra por Curso
     *
     * @param Builder $query
     * @param int $course
     * @return void
     */
    public function scopeWhereCourse(Builder $query, int $course): void
    {
        $query->where('ref_cod_curso', $course);
    }


    /**
     * Filtra diferentes series
     *
     * @param Builder $query
     * @param int $serie_exclude
     * @return void
     */
    public function scopeWhereNotGrade(Builder $query, int $serie_exclude): void
    {
        $query->where('cod_serie','<>',$serie_exclude);
    }

    /**
     * Filtra por séries presentes na escola
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
     * Filtra por Séries não presentes na escola
     *
     * @param Builder $query
     * @param int $school_exclude
     * @return void
     */
    public function scopeWhereNotSchool(Builder $query, int $school_exclude): void
    {
        $query->whereDoesntHave('schools', function ($q) use ($school_exclude) {
            $q->where('cod_escola', $school_exclude);
        });
    }

    /**
     * Ordena por nome e curso
     *
     * @param Builder $query
     * @return void
     */
    public function scopeOrderByNameAndCourse(Builder $query): void
    {
        $query->orderBy('nm_serie')->orderBy('ref_cod_curso');
    }
}
