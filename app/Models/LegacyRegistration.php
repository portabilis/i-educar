<?php

namespace App\Models;

use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property int $id
 * @property boolean isTransferred
 * @property boolean isAbandoned
 * @property LegacyStudentAbsence studentAbsence
 * @property LegacyStudentScore studentScore
 * @property LegacyStudentDescriptive studentDescriptive
 *
 */
class LegacyRegistration extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_aluno',
        'data_cadastro',
        'ano',
        'ref_usuario_cad',
        'dependencia',
        'ref_ref_cod_serie',
        'ref_cod_curso',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_matricula'
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
        return $this->cod_matricula;
    }

    /**
     * @return boolean
     */
    public function getIsDependencyAttribute()
    {
        return $this->dependencia;
    }

    /**
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->ano;
    }

    /**
     * Relação com o aluno.
     *
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * Relação com a escola.
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relação com a série.
     *
     * @return BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(LegacyLevel::class, 'ref_ref_cod_serie');
    }


    /**
     * @return HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    /**
     * @return HasMany
     */
    public function activeEnrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula')->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function lastEnrollment()
    {
        $hasOne = $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula');

        $hasOne->getQuery()->orderByDesc('sequencial');

        return $hasOne;
    }

    /**
     * @return HasMany
     */
    public function exemptions()
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_matricula', 'cod_matricula');
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

    public function getIsTransferredAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO;
    }

    public function getIsAbandonedAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::ABANDONO;
    }

    /**
     * @return HasOne
     */
    public function studentAbsence()
    {
        return $this->hasOne(LegacyStudentAbsence::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentScore()
    {
        return $this->hasOne(LegacyStudentScore::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentDescriptiveOpinion()
    {
        return $this->hasOne(LegacyStudentDescriptiveOpinion::class, 'matricula_id');
    }

    /**
     * @return HasMany
     */
    public function dependencies()
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
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
