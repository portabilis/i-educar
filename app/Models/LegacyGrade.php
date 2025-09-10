<?php

namespace App\Models;

use Ankurk91\Eloquent\HasBelongsToOne;
use Ankurk91\Eloquent\Relations\BelongsToOne;
use App\Models\Builders\LegacyGradeBuilder;
use App\Models\View\Discipline;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyGrade
 *
 * @method static LegacyGradeBuilder query()
 *
 * @property string $nm_serie
 * @property int $carga_horaria
 */
class LegacyGrade extends LegacyModel
{
    use HasBelongsToOne;

    /** @use HasBuilder<LegacyGradeBuilder> */
    use HasBuilder;

    public const CREATED_AT = 'data_cadastro';

    protected $table = 'pmieducar.serie';

    protected $primaryKey = 'cod_serie';

    protected static string $builder = LegacyGradeBuilder::class;

    public array $legacy = [
        'id' => 'cod_serie',
        'name' => 'nm_serie',
        'description' => 'descricao',
        'created_at' => 'data_cadastro',
        'course_id' => 'ref_cod_curso',
    ];

    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_cod_curso',
        'nm_serie',
        'etapa_curso',
        'concluinte',
        'carga_horaria',
        'data_cadastro',
        'ativo',
        'intervalo',
        'idade_inicial',
        'idade_final',
        'regra_avaliacao_id',
        'observacao_historico',
        'dias_letivos',
        'regra_avaliacao_diferenciada_id',
        'alerta_faixa_etaria',
        'bloquear_matricula_faixa_etaria',
        'idade_ideal',
        'exigir_inep',
        'importar_serie_pre_matricula',
        'descricao',
        'etapa_educacenso',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->descricao)) {
                    return $this->nm_serie;
                }

                return $this->nm_serie . ' (' . $this->descricao . ')';
            },
        );
    }

    /**
     * @return BelongsToMany<LegacyEvaluationRule, $this>
     */
    public function evaluationRules(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyEvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }

    /**
     * @return BelongsToOne
     */
    public function evaluationRule()
    {
        return $this->belongsToOne(
            LegacyEvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }

    /**
     * @return BelongsToOne
     */
    public function evaluationRuleDifferentiated()
    {
        return $this->belongsToOne(
            LegacyEvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_diferenciada_id'
        )->withPivot('ano_letivo');
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria,
        );
    }

    /**
     * @return HasMany<Discipline, $this>
     */
    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class, 'cod_serie');
    }

    /**
     * @return BelongsToMany<LegacySchool, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class, 'escola_serie', 'ref_cod_serie', 'ref_cod_escola')->wherePivot('ativo', 1);
    }

    /**
     * @return BelongsTo<LegacyCourse, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return HasMany<LegacySchoolClass, $this>
     */
    public function schoolClass(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_serie');
    }

    // TODO remover
    /**
     * @return HasMany<LegacySchoolGradeDiscipline, $this>
     */
    public function schoolGradeDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolGradeDiscipline::class, 'ref_ref_cod_serie');
    }

    // TODO remover
    /**
     * @return BelongsToMany<LegacyDiscipline, $this>
     */
    public function academicYearDisciplines(): BelongsToMany
    {
        return $this->belongsToMany(LegacyDiscipline::class, 'modules.componente_curricular_ano_escolar', 'ano_escolar_id', 'componente_curricular_id')
            ->withPivot(
                'hora_falta'
            );
    }

    /**
     * @return HasMany<LegacyDisciplineAcademicYear, $this>
     */
    public function allDisciplines(): HasMany
    {
        return $this->hasMany(LegacyDisciplineAcademicYear::class, 'ano_escolar_id');
    }
}
