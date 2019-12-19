<?php

namespace App\Models;

use App_Model_MatriculaSituacao;
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
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_curso',
        'ref_cod_aluno',
        'data_cadastro',
        'ano',
        'ref_usuario_cad',
        'dependencia',
        'ativo',
        'aprovado',
        'data_matricula',
        'ultima_matricula'
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

    public function getIsTransferredAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO;
    }

    public function getIsAbandonedAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::ABANDONO;
    }

    /**
     * @return HasMany
     */
    public function dependencies()
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
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
