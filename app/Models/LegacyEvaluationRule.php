<?php

namespace App\Models;

use App\Models\Builders\LegacyEvaluationRuleBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyEvaluationRule
 *
 * @method static LegacyEvaluationRuleBuilder query()
 *
 * @property int $tipo_recuperacao_paralela
 * @property int $tipo_calculo_recuperacao_paralela
 * @property int $tipo_presenca
 * @property int $parecer_descritivo
 * @property int $nota_geral_por_etapa
 * @property string $nome
 */
class LegacyEvaluationRule extends Model
{
    /** @use HasBuilder<LegacyEvaluationRuleBuilder> */
    use HasBuilder;

    public const CREATED_AT = null;

    public const PARALLEL_REMEDIAL_NONE = 0;

    public const PARALLEL_REMEDIAL_PER_STAGE = 1;

    public const PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE = 2;

    public const PARALLEL_REMEDIAL_REPLACE_SCORE = 1;

    public const PARALLEL_REMEDIAL_AVERAGE_SCORE = 2;

    public const PARALLEL_REMEDIAL_SUM_SCORE = 3;

    protected $table = 'modules.regra_avaliacao';

    protected $casts = [
        'media_recuperacao_paralela' => 'float',
    ];

    protected static string $builder = LegacyEvaluationRuleBuilder::class;

    public array $legacy = [
        'name' => 'nome',
    ];

    protected $fillable = [
        'instituicao_id',
        'nome',
        'formula_media_id',
        'formula_recuperacao_id',
        'tipo_nota',
        'tipo_progressao',
        'tipo_presenca',
        'pontos',
        'tipo_recuperacao_paralela',
    ];

    /**
     * @return HasMany<LegacyRemedialRule, $this>
     */
    public function remedialRules(): HasMany
    {
        return $this->hasMany(LegacyRemedialRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return BelongsTo<LegacyRoundingTable, $this>
     */
    public function roundingTable(): BelongsTo
    {
        return $this->belongsTo(LegacyRoundingTable::class, 'tabela_arredondamento_id');
    }

    /**
     * @return BelongsTo<LegacyRoundingTable, $this>
     */
    public function conceptualRoundingTable(): BelongsTo
    {
        return $this->belongsTo(LegacyRoundingTable::class, 'tabela_arredondamento_id_conceitual');
    }

    /**
     * @return BelongsTo<LegacyEvaluationRule, $this>
     */
    public function deficiencyEvaluationRule(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'regra_diferenciada_id');
    }

    public function isAverageBetweenScoreAndRemedialCalculation(): bool
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_AVERAGE_SCORE;
    }

    public function isSpecificRetake(): bool
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE;
    }

    public function isNone(): bool
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_NONE;
    }

    public function isByStage(): bool
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE;
    }

    public function isSumScoreCalculation(): bool
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_SUM_SCORE;
    }

    public function isGeneralAbsence(): bool
    {
        return $this->tipo_presenca === 1;
    }

    public function hasOpinion(): bool
    {
        return $this->parecer_descritivo !== 0;
    }

    public function isGeneralScore(): bool
    {
        return $this->nota_geral_por_etapa === 1;
    }

    public function isGlobalScore(): bool
    {
        return $this->nota_geral_por_etapa == 1;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome,
        );
    }

    /**
     * @return HasMany<LegacyEvaluationRuleGradeYear, $this>
     */
    public function gradeYears(): HasMany
    {
        return $this->hasMany(LegacyEvaluationRuleGradeYear::class, 'regra_avaliacao_id');
    }

    /**
     * @return BelongsTo<LegacyAverageFormula, $this>
     */
    public function averageFormula(): BelongsTo
    {
        return $this->belongsTo(LegacyAverageFormula::class, 'formula_media_id');
    }

    /**
     * @return BelongsTo<LegacyAverageFormula, $this>
     */
    public function recoveryFormula(): BelongsTo
    {
        return $this->belongsTo(LegacyAverageFormula::class, 'formula_recuperacao_id');
    }
}
