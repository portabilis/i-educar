<?php

namespace App\Models;

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
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_turma',
        'sgl_turma',
        'max_aluno',
        'data_cadastro',
        'ref_cod_turma_tipo',
        'ref_ref_cod_escola',
        'ref_ref_cod_serie',
        'ref_cod_curso',
        'ref_cod_infra_predio_comodo',
        'visivel',
        'multiseriada',
        'ativo',
        'hora_inicial',
        'hora_final',
        'hora_inicio_intervalo',
        'hora_fim_intervalo',
        'ref_cod_regente',
        'ref_cod_instituicao_regente',
        'ref_cod_instituicao',
        'ref_ref_cod_serie_mult',
        'ref_ref_cod_escola_mult',
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
        'atividades_complementares',
        'atividades_aee',
        'local_funcionamento_diferenciado',
        'dias_semana',
        'tipo_boletim_diferenciado',
        'tipo_mediacao_didatico_pedagogico',
        'ref_cod_disciplina_dispensada',
        'etapa_educacenso',
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

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_turma;
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
     * @return string
     */
    public function getBeginAcademicYearAttribute()
    {
        return $this->stages()->orderBy('sequencial')->first()->data_inicio;
    }

    /**
     * @return string
     */
    public function getEndAcademicYearAttribute()
    {
        return $this->stages()->orderByDesc('sequencial')->first()->data_fim;
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

        return (boolean) $schoolGrade->bloquear_enturmacao_sem_vagas;
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
}
