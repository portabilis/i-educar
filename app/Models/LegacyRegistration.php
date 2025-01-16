<?php

namespace App\Models;

use Ankurk91\Eloquent\HasBelongsToOne;
use Ankurk91\Eloquent\Relations\BelongsToOne;
use App\Models\Builders\LegacyRegistrationBuilder;
use App\Models\View\Situation;
use App\Traits\HasLegacyDates;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property int                    $id
 * @property bool                   $isTransferred
 * @property bool                   $isAbandoned
 * @property bool                   $isCanceled
 * @property bool                   $bloquear_troca_de_situacao
 * @property bool                   $dependencia
 * @property int                    $cod_matricula
 * @property int                    $ref_ref_cod_escola
 * @property int                    $ano
 * @property int                    $aprovado
 * @property int                    $ativo
 * @property LegacyStudentAbsence   $studentAbsence
 * @property LegacyStudentScore     $studentScore
 * @property LegacyCourse           $course
 * @property LegacySchool           $school
 * @property Collection             $enrollments
 * @property array<int, string> $fillable
 * @property LegacyStudent $student
 */
class LegacyRegistration extends LegacyModel
{
    use HasBelongsToOne;

    /** @use HasBuilder<LegacyRegistrationBuilder> */
    use HasBuilder;

    use HasLegacyDates;

    protected $table = 'pmieducar.matricula';

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'cod_matricula';

    protected static string $builder = LegacyRegistrationBuilder::class;

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
        'ref_cod_abandono_tipo',
        'data_cancel',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_matricula' => 'date',
        'data_cancel' => 'date',
        'data_exclusao' => 'date',
    ];

    /**
     * @var array<string, string>
     */
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
     *
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * Relação com a escola.
     *
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relação com a série.
     *
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * Relação com o curso.
     *
     * @return BelongsTo<LegacyCourse, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return HasMany<LegacyEnrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    /**
     * @return BelongsToMany<LegacySchoolClass, $this>
     */
    public function schoolClasses(): BelongsToMany
    {
        // @phpstan-ignore-next-line
        return $this->belongsToMany(
            LegacySchoolClass::class,
            'pmieducar.matricula_turma',
            'ref_cod_matricula',
            'ref_cod_turma',
        )->where('pmieducar.turma.ativo', 1);
    }

    /**
     * @return HasMany<Situation, $this>
     */
    public function situations(): HasMany
    {
        return $this->hasMany(Situation::class, 'cod_matricula');
    }

    /**
     * @return HasOne<Situation, $this>
     */
    public function situation(): HasOne
    {
        return $this->hasOne(Situation::class, 'cod_matricula');
    }

    /**
     * @return HasOne<Situation, $this>
     */
    public function situationApproved(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(Situation::class, 'cod_matricula')->approved();
    }

    public function schoolClass(): BelongsToOne
    {
        // @phpstan-ignore-next-line
        return $this->belongsToOne(
            LegacySchoolClass::class,
            'pmieducar.matricula_turma',
            'ref_cod_matricula',
            'ref_cod_turma',
        )->where('pmieducar.turma.ativo', 1)
            ->orderBy('matricula_turma.sequencial', 'desc');
    }

    /**
     * @return HasOne<LegacyTransferRequest, $this>
     */
    public function transferStart(): HasOne
    {
        return $this->hasOne(LegacyTransferRequest::class, 'ref_cod_matricula_entrada');
    }

    /**
     * @return HasOne<LegacyTransferRequest, $this>
     */
    public function transferEnd(): HasOne
    {
        return $this->hasOne(LegacyTransferRequest::class, 'ref_cod_matricula_saida');
    }

    /**
     * @return HasMany<LegacyRegistrationScore, $this>
     */
    public function registrationStores(): HasMany
    {
        return $this->hasMany(LegacyRegistrationScore::class, 'matricula_id');
    }

    /**
     * @return HasMany<LegacyDisciplineScore, $this>
     */
    public function disciplineScores(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany<LegacyEnrollment, $this>
     */
    public function activeEnrollments(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula')->where('ativo', 1);
    }

    /**
     * @return HasOne<LegacyEnrollment, $this>
     */
    public function lastEnrollment(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula')->orderBy('sequencial', 'DESC');
    }

    /**
     * @return HasMany<LegacyDisciplineExemption, $this>
     */
    public function exemptions(): HasMany
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return HasMany<LegacyActiveLooking, $this>
     */
    public function activeLookings(): HasMany
    {
        return $this->hasMany(LegacyActiveLooking::class, 'ref_cod_matricula', 'cod_matricula');
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

    /**
     * @return HasOne<LegacyStudentAbsence, $this>
     */
    public function studentAbsence(): HasOne
    {
        return $this->hasOne(LegacyStudentAbsence::class, 'matricula_id');
    }

    /**
     * @return HasOne<LegacyStudentScore, $this>
     */
    public function studentScore(): HasOne
    {
        return $this->hasOne(LegacyStudentScore::class, 'matricula_id');
    }

    /**
     * @return HasOne<LegacyStudentDescriptiveOpinion, $this>
     */
    public function studentDescriptiveOpinion(): HasOne
    {
        return $this->hasOne(LegacyStudentDescriptiveOpinion::class, 'matricula_id');
    }

    /**
     * @return HasMany<LegacyDisciplineDependence, $this>
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        /** @var LegacyEvaluationRuleGradeYear $evaluationRuleGradeYear */
        $evaluationRuleGradeYear = $this->evaluationRuleGradeYear()
            ->where('ano_letivo', $this->ano)
            ->firstOrFail();

        if ($this->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            return $evaluationRuleGradeYear->differentiatedEvaluationRule;
        }

        return $evaluationRuleGradeYear->evaluationRule;
    }

    /**
     * @return BelongsTo<LegacyEvaluationRuleGradeYear, $this>
     */
    public function evaluationRuleGradeYear(): BelongsTo
    {
        return $this->belongsTo(LegacyEvaluationRuleGradeYear::class, 'ref_ref_cod_serie', 'serie_id');
    }

    protected function statusDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistrationStatus)->getDescriptiveValues()[(int) $this->aprovado]
        );
    }
}
