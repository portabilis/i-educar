<?php

namespace App\Models;

use Ankurk91\Eloquent\HasBelongsToOne;
use App\Models\Builders\LegacyGradeBuilder;
use App\Models\View\Discipline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyGrade
 *
 * @method static LegacyGradeBuilder query()
 */
class LegacyGrade extends LegacyModel
{
    use HasBelongsToOne;

    public const CREATED_AT = 'data_cadastro';

    /**
     * @var string
     */
    protected $table = 'pmieducar.serie';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_serie';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacyGradeBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'cod_serie',
        'name' => 'nm_serie',
        'description' => 'descricao',
        'created_at' => 'data_cadastro',
        'course_id' => 'ref_cod_curso',
    ];

    /**
     * @var array
     */
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
        'etapa_educacenso'
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
     * Regras de avaliação
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

    public function evaluationRule()
    {
        return $this->belongsToOne(
            LegacyEvaluationRule::class,
            'modules.regra_avaliacao_serie_ano',
            'serie_id',
            'regra_avaliacao_id'
        )->withPivot('ano_letivo', 'regra_avaliacao_diferenciada_id');
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria,
        );
    }

    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class, 'cod_serie');
    }

    /**
     * Escolas
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class, 'escola_serie', 'ref_cod_serie', 'ref_cod_escola')->wherePivot('ativo', 1);
    }

    /**
     * Relacionamento com o curso.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * Relacionamento com a turma.
     */
    public function schoolClass(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_serie');
    }

    // TODO remover
    public function schoolGradeDisciplines()
    {
        return $this->hasMany(LegacySchoolGradeDiscipline::class, 'ref_ref_cod_serie');
    }

    // TODO remover
    public function academicYearDisciplines()
    {
        return $this->belongsToMany(LegacyDiscipline::class, 'modules.componente_curricular_ano_escolar', 'ano_escolar_id', 'componente_curricular_id')
            ->withPivot(
                'hora_falta'
            );
    }

    public function allDisciplines(): HasMany
    {
        return $this->hasMany(LegacyDisciplineAcademicYear::class, 'ano_escolar_id');
    }
}
