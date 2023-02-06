<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolClassBuilder;
use App\Models\View\Discipline;
use App\Traits\LegacyAttribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property LegacyGrade        $grade
 * @property LegacySchool       $school
 * @property LegacySchoolGrade  $schoolGrade
 * @property LegacyEnrollment[] $enrollments
 *
 * @method static LegacySchoolClassBuilder query()
 */
class LegacySchoolClass extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    public const CREATED_AT = 'data_cadastro';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = LegacySchoolClassBuilder::class;

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

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_turma,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->year)) {
                    return $this->nm_turma;
                }

                return $this->nm_turma . ' (' . $this->year . ')';
            },
        );
    }

    protected function year(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano,
        );
    }

    protected function schoolId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_ref_cod_escola,
        );
    }

    protected function courseId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_curso,
        );
    }

    protected function gradeId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_ref_cod_serie,
        );
    }

    protected function visible(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->visivel,
        );
    }

    protected function exemptedDisciplineId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_disciplina_dispensada,
        );
    }

    protected function vacancies(): Attribute
    {
        return Attribute::make(
            get: function () {
                $vacancies = $this->max_aluno - $this->getTotalEnrolled();

                return max($vacancies, 0);
            },
        );
    }

    /**
     * Retorna o total de alunos enturmados desconsiderando matrículas de
     * dependência.
     *
     * @return int
     */
    public function getTotalEnrolled(): int
    {
        return $this->enrollments()
            ->where('ativo', 1)
            ->whereHas('registration', function ($query) {
                $query->where('dependencia', false);
            })->count();
    }

    protected function beginAcademicYear(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stages()->orderBy('sequencial')->value('data_inicio'),
        );
    }

    protected function endAcademicYear(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stages()->orderByDesc('sequencial')->value('data_fim'),
        );
    }

    /**
     * Séries
     *
     * @return BelongsToMany
     */
    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(LegacyGrade::class, 'pmieducar.turma_serie', 'turma_id', 'serie_id');
    }

    /**
     * Anos Letivos
     *
     * @return HasMany
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(LegacySchoolAcademicYear::class, 'ref_cod_escola', 'ref_ref_cod_escola')->whereColumn('escola_ano_letivo.ano', 'ano');
    }

    /**
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * Relacionamento com a escola.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relacionamento com as enturmações.
     *
     * @return HasMany
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return HasMany
     */
    public function stages(): HasMany
    {
        if ($this->course?->is_standard_calendar) {
            return $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola', 'ref_ref_cod_escola')
                ->where('ref_ano', $this->year);
        }

        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * @return HasMany
     */
    public function schoolClassStages(): HasMany
    {
        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma', 'cod_turma');
    }

    public function multigrades(): HasMany
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
            $value = explode(',', str_replace([
                '{',
                '}'
            ], '', $value));
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
                    $query->whereIn('aprovado', [
                        1,
                        2,
                        3
                    ]);
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
    public function schoolGrade(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolGrade::class, 'ref_ref_cod_escola', 'ref_cod_escola')
            ->where('ref_cod_serie', $this->grade_id);
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

        return (bool)$schoolGrade->bloquear_enturmacao_sem_vagas;
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
    public function disciplines(): BelongsToMany
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

    public function viewDisciplines(): HasMany
    {
        return $this->hasMany(Discipline::class, 'cod_turma', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function gradeDisciplines(): BelongsToMany
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
    public function getDisciplines(): Collection
    {
        if ((bool)$this->multiseriada) {
            $multigrades = $this->multigrades->pluck('serie_id')->toArray();

            return LegacySchoolGradeDiscipline::query()
                ->where('ref_ref_cod_escola', $this->school_id)
                ->whereIn('ref_ref_cod_serie', $multigrades)
                ->whereRaw('? = ANY(anos_letivos)', [$this->year])
                ->get()
                ->map(function ($schoolGrade) {
                    return $schoolGrade->discipline;
                });
        }
        $disciplinesOfSchoolClass = $this->disciplines()->get();
        if ($disciplinesOfSchoolClass->count() > 0) {
            return $disciplinesOfSchoolClass;
        }

        return LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_escola', $this->school_id)
            ->where('ref_ref_cod_serie', $this->grade_id)
            ->whereRaw('? = ANY(anos_letivos)', [$this->year])
            ->get()
            ->map(function ($schoolGrade) {
                return $schoolGrade->discipline;
            });
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
     * Retorna o turno da turma.
     *
     * @return BelongsTo
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'turma_turno_id');
    }

    public function inep(): HasOne
    {
        return $this->hasOne(SchoolClassInep::class, 'cod_turma');
    }
}
