<?php

namespace App\Models;

use Ankurk91\Eloquent\HasBelongsToOne;
use App\Models\Builders\LegacyRegistrationBuilder;
use App\Models\View\Situation;
use App\Traits\HasLegacyDates;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property int              $id
 * @property bool              $isTransferred
 * @property bool              $isAbandoned
 * @property bool              $isCanceled
 * @property bool              $bloquear_troca_de_situacao
 * @property bool              $dependencia
 * @property int              $cod_matricula
 * @property int              $ano
 * @property LegacyStudentAbsence $studentAbsence
 * @property LegacyStudentScore   $studentScore
 * @property LegacyCourse         $course
 * @property Collection           $enrollments
 */
class LegacyRegistration extends LegacyModel
{
    use HasBelongsToOne;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = 'updated_at';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    protected string $builder = LegacyRegistrationBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_curso',
        'ref_cod_aluno',
        'ano',
        'ref_usuario_cad',
        'dependencia',
        'ativo',
        'aprovado',
        'data_matricula',
        'ultima_matricula',
        'bloquear_troca_de_situacao',
        'observacao',
    ];

    protected $casts = [
        'data_matricula' => 'date',
        'data_cancel' => 'date',
        'data_exclusao' => 'date',
    ];

    public array $legacy = [
        'id' => 'cod_matricula',
        'student_id' => 'ref_cod_aluno',
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_matricula
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->student->name
        );
    }

    public function isLockedToChangeStatus(): bool
    {
        return (bool) $this->bloquear_troca_de_situacao;
    }

    protected function isDependency(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dependencia
        );
    }

    protected function year(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano
        );
    }

    protected function schoolId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_ref_cod_escola
        );
    }

    /**
     * Relação com o aluno.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * Relação com a escola.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relação com a série.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * Relação com o curso.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacySchoolClass::class,
            'pmieducar.matricula_turma',
            'ref_cod_matricula',
            'ref_cod_turma',
        )->where('pmieducar.turma.ativo', 1);
    }

    public function situations(): HasMany
    {
        return $this->hasMany(Situation::class, 'cod_matricula');
    }

    public function situation(): HasOne
    {
        return $this->hasOne(Situation::class, 'cod_matricula');
    }

    public function situationApproved(): HasOne
    {
        return $this->hasOne(Situation::class, 'cod_matricula')->approved();
    }

    public function schoolClass()
    {
        return $this->belongsToOne(
            LegacySchoolClass::class,
            'pmieducar.matricula_turma',
            'ref_cod_matricula',
            'ref_cod_turma',
        )->where('pmieducar.turma.ativo', 1)
            ->orderBy('matricula_turma.sequencial', 'desc');
    }

    public function transferStart(): HasOne
    {
        return $this->hasOne(LegacyTransferRequest::class, 'ref_cod_matricula_entrada');
    }

    public function transferEnd(): HasOne
    {
        return $this->hasOne(LegacyTransferRequest::class, 'ref_cod_matricula_saida');
    }

    public function registrationStores(): HasMany
    {
        return $this->hasMany(LegacyRegistrationScore::class, 'matricula_id');
    }

    public function disciplineScores(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula')->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function lastEnrollment()
    {
        return $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula')->orderBy('sequencial', 'DESC');
    }

    public function exemptions(): HasMany
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_matricula', 'cod_matricula');
    }

    public function activeLookings(): HasMany
    {
        return $this->hasMany(LegacyActiveLooking::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('matricula.ativo', 1);
    }

    protected function isTransferred(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO
        );
    }

    protected function isAbandoned(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->aprovado == App_Model_MatriculaSituacao::ABANDONO
        );
    }

    protected function isCanceledA(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ativo === 0
        );
    }

    public function studentAbsence(): HasOne
    {
        return $this->hasOne(LegacyStudentAbsence::class, 'matricula_id');
    }

    public function studentScore(): HasOne
    {
        return $this->hasOne(LegacyStudentScore::class, 'matricula_id');
    }

    public function studentDescriptiveOpinion(): HasOne
    {
        return $this->hasOne(LegacyStudentDescriptiveOpinion::class, 'matricula_id');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        $evaluationRuleGradeYear = $this->evaluationRuleGradeYear()
            ->where('ano_letivo', $this->ano)
            ->firstOrFail();

        if ($this->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            return $evaluationRuleGradeYear->differentiatedEvaluationRule;
        }

        return $evaluationRuleGradeYear->evaluationRule;
    }

    public function evaluationRuleGradeYear(): BelongsTo
    {
        return $this->belongsTo(LegacyEvaluationRuleGradeYear::class, 'ref_ref_cod_serie', 'serie_id');
    }

    protected function statusDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistrationStatus())->getDescriptiveValues()[(int) $this->aprovado]
        );
    }

    public function scopeMale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'F');
    }

    public function scopeLastYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y') - 1);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y'));
    }
}
