<?php

namespace App\Models;

use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;

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
        return $this->getRawOriginal('id') ?? $this->cod_curso;
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
        return $this->getRawOriginal('steps') ?? $this->qtd_etapas;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->getRawOriginal('name') ?? $this->nm_curso;
    }

    /**
     * @return bool
     */
    public function getIsStandardCalendarAttribute()
    {
        return$this->getRawOriginal('is_standard_calendar') ??  $this->padrao_ano_escolar;
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
     * @param $institution
     * @return void
     */
    public function scopeWhereInstitution(Builder $query, ?int $institution = null): void
    {
        if ($institution !== null) {
            $query->where('ref_cod_instituicao', $institution);
        }
    }

    /**
     * Filtra por Escola
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
     * Filtra por Padrão Ano Escolar
     *
     * @param Builder $query
     * @param bool|null $condition
     * @return void
     */
    public function scopeWhereNotIsStandardCalendar(Builder $query, ?bool $condition = true): void
    {
        if ($condition) {
            $query->where('padrao_ano_escolar',0);
        }
    }

    /**
     * Filtra o Curso
     *
     * @param Builder $query
     * @param int|null $course
     * @return void
     */
    public function scopeWhereCourse(Builder $query, ?int $course = null): void
    {
        if ($course) {
            $query->where('cod_curso',$course);
        }
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
            $query->addSelect(\DB::raw("(CASE WHEN coalesce(descricao,'') <> '' THEN (nm_curso || ' (' || descricao || ')') ELSE nm_curso END) as name"));
        } else {
            $query->addSelect("name");
        }
    }

    /**
     * Ordena por nome
     *
     * @param Builder $query
     * @return void
     */
    public function scopeOrderByName(Builder $query): void
    {
        $query->orderBy('nm_curso');
    }
}
