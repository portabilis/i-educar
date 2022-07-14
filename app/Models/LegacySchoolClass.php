<?php

namespace App\Models;

use App\Models\Builders\LegacyBuilder;
use App\Models\Builders\LegacySchoolClassBuilder;
use App\Traits\LegacyAttribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacySchoolClass
 *
 * @property int                $id
 * @property string             $name
 * @property int                $year
 * @property int                $school_id
 * @property int                $course_id
 * @property int                $grade_id
 * @property int                $vacancies
 * @property bool               $multiseriada
 * @property int                $exempted_discipline_id
 * @property Carbon             $begin_academic_year
 * @property Carbon             $end_academic_year
 * @property LegacyCourse       $course
 * @property LegacyLevel        $grade
 * @property LegacySchool       $school
 * @property LegacySchoolGrade  $schoolGrade
 * @property LegacyEnrollment[] $enrollments
 */
class LegacySchoolClass extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';


    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacySchoolClassBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'cod_turma',
        'name' => 'nm_turma',
        'year' => 'ano'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_infra_predio_comodo',
        'nm_turma',
        'sgl_turma',
        'max_aluno',
        'multiseriada',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_turma_tipo',
        'hora_inicial',
        'hora_final',
        'hora_inicio_intervalo',
        'hora_fim_intervalo',
        'ref_cod_regente',
        'ref_cod_instituicao_regente',
        'ref_cod_instituicao',
        'ref_cod_curso',
        'ref_ref_cod_serie_mult',
        'ref_ref_cod_escola_mult',
        'visivel',
        'tipo_boletim',
        'turma_turno_id',
        'ano',
        'tipo_atendimento',
        'turma_mais_educacao',
        'atividade_complementar_1',
        'atividade_complementar_2',
        'atividade_complementar_3',
        'atividade_complementar_4',
        'atividade_complementar_5',
        'atividade_complementar_6',
        'aee_braille',
        'aee_recurso_optico',
        'aee_estrategia_desenvolvimento',
        'aee_tecnica_mobilidade',
        'aee_libras',
        'aee_caa',
        'aee_curricular',
        'aee_soroban',
        'aee_informatica',
        'aee_lingua_escrita',
        'aee_autonomia',
        'cod_curso_profissional',
        'etapa_educacenso',
        'ref_cod_disciplina_dispensada',
        'parecer_1_etapa',
        'parecer_2_etapa',
        'parecer_3_etapa',
        'parecer_4_etapa',
        'nao_informar_educacenso',
        'tipo_mediacao_didatico_pedagogico',
        'tipo_boletim_diferenciado',
        'dias_semana',
        'atividades_complementares',
        'atividades_aee',
        'local_funcionamento_diferenciado',
        'estrutura_curricular',
        'formas_organizacao_turma',
        'unidade_curricular',
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
        return $this->cod_turma;
    }

    public function toArray() {
        $data = parent::toArray();
        $data->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        });
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        if (empty($this->year)) {
            return $this->nm_turma;
        }

        return $this->nm_turma . ' (' . $this->year . ')';
    }

    /**
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->ano;
    }

    /**
     * @return int
     */
    public function getSchoolIdAttribute()
    {
        return $this->ref_ref_cod_escola;
    }

    /**
     * @return int
     */
    public function getCourseIdAttribute()
    {
        return $this->ref_cod_curso;
    }

    /**
     * @return int
     */
    public function getGradeIdAttribute()
    {
        return $this->ref_ref_cod_serie;
    }

    /**
     * @return int
     */
    public function getExemptedDisciplineIdAttribute()
    {
        return $this->ref_cod_disciplina_dispensada;
    }

    /**
     * @return int
     */
    public function getVacanciesAttribute()
    {
        $vacancies = $this->max_aluno - $this->getTotalEnrolled();

        return $vacancies > 0 ? $vacancies : 0;
    }

    /**
     * Retorna o total de alunos enturmados desconsiderando matrículas de
     * dependência.
     *
     * @return int
     */
    public function getTotalEnrolled()
    {
        return $this->enrollments()
            ->where('ativo', 1)
            ->whereHas('registration', function ($query) {
                $query->where('dependencia', false);
            })->count();
    }

    /**
     * @return string|null
     */
    public function getBeginAcademicYearAttribute()
    {
        $calendar = $this->stages()->orderBy('sequencial')->first();

        return $calendar ? $calendar->data_inicio : null;
    }

    /**
     * @return string|null
     */
    public function getEndAcademicYearAttribute()
    {
        $calendar = $this->stages()->orderByDesc('sequencial')->first();

        return $calendar ? $calendar->data_fim : null;
    }

    /**
     * Séries
     *
     * @return BelongsToMany
     */
    public function grades(): BelongsToMany {
        return $this->belongsToMany(LegacyGrade::class,'turma_serie','turma_id','serie_id');
    }


    /**
     * Anos Letivos
     *
     * @return HasMany
     */
    public function academic_years(): HasMany {
        return $this->hasMany(LegacySchoolAcademicYear::class,'ref_cod_escola','ref_ref_cod_escola')->whereColumn('escola_ano_letivo.ano','turma.ano');
    }

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(LegacyLevel::class, 'ref_ref_cod_serie');
    }

    /**
     * Relacionamento com a escola.
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relacionamento com as enturmações.
     *
     * @return HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return HasMany
     */
    public function stages()
    {
        if ($this->course->is_standard_calendar) {
            return $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola', 'ref_ref_cod_escola')
                ->where('ref_ano', $this->year);
        }

        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return HasMany
     */
    public function schoolClassStages()
    {
        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma', 'cod_turma');
    }

    public function multigrades()
    {
        return $this->hasMany(LegacySchoolClassGrade::class, 'turma_id');
    }

    /**
     * Retorna os dias da semana em um array
     *
     * @param string $value
     *
     * @return array|null
     */
    public function getDiasSemanaAttribute($value)
    {
        if (is_string($value)) {
            $value = explode(',', str_replace(['{', '}'], '', $value));
        }

        return $value;
    }

    /**
     * Seta os dias da semana transformando um array em uma string
     *
     * @param array $values
     *
     * @return void
     */
    public function setDiasSemanaAttribute($values)
    {
        if (is_array($values)) {
            $values = '{' . implode(',', $values) . '}';
        }
        $this->attributes['dias_semana'] = $values;
    }

    /**
     * @return Collection
     */
    public function getActiveEnrollments()
    {
        return $this->enrollments()
            ->with([
                'registration' => function ($query) {
                    /** @var Builder $query */
                    $query->where('ano', $this->year);
                    $query->whereIn('aprovado', [1, 2, 3]);
                    $query->with('student.person');
                }
            ])
            ->where('ativo', 1)
            ->orderBy('sequencial_fechamento')
            ->get();
    }

    /**
     * @return BelongsTo
     */
    public function schoolGrade()
    {
        $belongsTo = $this->belongsTo(LegacySchoolGrade::class, 'ref_ref_cod_escola', 'ref_cod_escola')
            ->where('ref_cod_serie', $this->grade_id);

        return $belongsTo;
    }

    /**
     * Indica se bloqueia enturmações quando não houver vagas.
     *
     * @return bool
     */
    public function denyEnrollmentsWhenNoVacancy()
    {
        $schoolGrade = $this->schoolGrade;

        if (empty($schoolGrade)) {
            return true;
        }

        if (empty($schoolGrade->bloquear_enturmacao_sem_vagas)) {
            return true;
        }

        return (bool) $schoolGrade->bloquear_enturmacao_sem_vagas;
    }

    /**
     * Retorna o tempo de aula da turma em horas
     *
     * @return int
     */
    public function getClassTime()
    {
        if (!$this->hora_inicial || !$this->hora_final) {
            return 0;
        }

        $startTime = Carbon::createFromTimeString($this->hora_inicial);
        $endTime = Carbon::createFromTimeString($this->hora_final);

        return $startTime->diff($endTime)->h;
    }

    /**
     * @return BelongsToMany
     */
    public function disciplines()
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'modules.componente_curricular_turma',
            'turma_id',
            'componente_curricular_id'
        )->withPivot([
            'ano_escolar_id',
            'escola_id',
            'carga_horaria',
            'docente_vinculado',
            'etapas_especificas',
            'etapas_utilizadas',
        ]);
    }

    /**
     * @return BelongsToMany
     */
    public function gradeDisciplines()
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'modules.componente_curricular_ano_escolar',
            'ano_escolar_id',
            'componente_curricular_id',
            'ref_ref_cod_serie',
            'id'
        )->withPivot([
            'carga_horaria',
            'tipo_nota',
        ]);
    }

    /**
     * @return Collection
     */
    public function getDisciplines()
    {
        if ($this->course->is_standard_calendar) {
            return $this->gradeDisciplines()
                ->whereRaw('? = ANY(anos_letivos)', [$this->year])
                ->get();
        }

        return $this->disciplines()
            ->where('ano_escolar_id', $this->grade_id)
            ->where('escola_id', $this->school_id)
            ->get();
    }

    /**
     * Retorna a regra de avaliação que deve ser utilizada para a turma. Leva
     * em consideração o parâmetro `utiliza_regra_diferenciada` da escola.
     *
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        $evaluationRuleGradeYear = $this->hasOne(LegacyEvaluationRuleGradeYear::class, 'serie_id', 'ref_ref_cod_serie')
            ->where('ano_letivo', $this->ano)
            ->firstOrFail();

        if ($this->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            return $evaluationRuleGradeYear->differentiatedEvaluationRule;
        }

        return $evaluationRuleGradeYear->evaluationRule;
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
     * Ordena por nome
     *
     * @param Builder $query
     * @param string $direction
     * @return void
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('nm_turma',$direction);
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
     * Filtra por ano escolar em progresso
     *
     * @param Builder $query
     * @param int|null $year
     * @return void
     */
    public function scopeWhereInProgress(Builder $query): void
    {
        $query->whereHas('academic_years',function ($q){
            $q->inProgress();
        });
    }

    /**
     * Filtra pelo ano e em progresso
     *
     * @param Builder $query
     * @param int $year
     * @return void
     */
    public function scopeWhereInProgressYear(Builder $query, int $year): void
    {
        $query->whereHas('academic_years',function ($q) use($year){
            $q->inProgress();
            $q->whereYear($year);
        });
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
        $query->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Serie
     *
     * @param Builder $query
     * @param int $grade
     * @return void
     */
    public function scopeWhereGrade(Builder $query, int $grade): void
    {
        $query->where(function ($q) use($grade){
            $q->whereHas('grades',function ($q) use($grade){
                $q->where('cod_serie',$grade);
            });
            $q->orWhere('ref_ref_cod_serie',$grade);
        });
    }

    /**
     * Retorna o turno da turma.
     *
     * Relação com turma_turno.
     *
     * @return bool | string
     */
    public function period()
    {
        return $this->belongsTo(LegacyPeriod::class, 'turma_turno_id');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }
}
