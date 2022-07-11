<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyGrade extends Model
{
    use HasFactory;

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
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->getRawOriginal('id') ?? $this->cod_serie;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->getRawOriginal('name') ?? $this->nm_serie;
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
     * Adiciona ao select o nome com descrição
     *
     * @param Builder $query
     * @param bool $withDescription
     */
    public function scopeSelectName(Builder $query, bool $withDescription = true): void
    {
        if ($withDescription) {
            $query->addSelect(\DB::raw("(CASE WHEN coalesce(descricao,'') <> '' THEN (nm_serie || ' (' || descricao || ')') ELSE nm_serie END) as name"));
        } else {
            $query->addSelect("name");
        }
    }

    /**
     * Filtra por Curso
     */
    public function scopeWhereCourse(Builder $query, ?int $course = null): void
    {
        if ($course !== null) {
            $query->where('ref_cod_curso', $course);
        }
    }


    /**
     * Filtra diferentes
     *
     * @param Builder $query
     * @param int|null $serie_exclude
     * @return void
     */
    public function scopeWhereNotGrade(Builder $query, ?int $serie_exclude = null): void
    {
        if ($serie_exclude !== null) {
            $query->where('cod_serie','<>',$serie_exclude);
        }
    }

    /**
     * Filtra por séries presentes na escola
     *
     * @param Builder $query
     * @param int|null $school
     * @return void
     */
    public function scopeWhereSchool(Builder $query, ?int $school = null): void
    {
        if ($school !== null) {
            $query->whereHas('schools', function ($q) use ($school) {
                $q->where('cod_escola', $school);
            });
        }
    }

    /**
     * Filtra por Séries não presentes na escola
     *
     * @param Builder $query
     * @param int|null $school_exclude
     * @return void
     */
    public function scopeWhereNotSchool(Builder $query, ?int $school_exclude = null): void
    {
        if ($school_exclude !== null) {

            $query->whereDoesntHave('schools', function ($q) use ($school_exclude) {
                $q->where('cod_escola', $school_exclude);
            });
        }
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
