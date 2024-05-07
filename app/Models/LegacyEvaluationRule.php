<?php

namespace App\Models;

use App\Models\Builders\LegacyEvaluationRuleBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyEvaluationRule
 *
 * @method static LegacyEvaluationRuleBuilder query()
 */
class LegacyEvaluationRule extends Model
{
    use LegacyAttribute;

    public const CREATED_AT = null;

    public const PARALLEL_REMEDIAL_NONE = 0;

    public const PARALLEL_REMEDIAL_PER_STAGE = 1;

    public const PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE = 2;

    public const PARALLEL_REMEDIAL_REPLACE_SCORE = 1;

    public const PARALLEL_REMEDIAL_AVERAGE_SCORE = 2;

    public const PARALLEL_REMEDIAL_SUM_SCORE = 3;

    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao';

    /**
     * @var array
     */
    protected $casts = [
        'media_recuperacao_paralela' => 'float',
    ];

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacyEvaluationRuleBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'name' => 'nome',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id',
        'nome',
        'formula_media_id',
        'formula_recuperacao_id',
        'tipo_nota',
        'tipo_progressao',
        'tipo_presenca',
    ];

    /**
     * @return HasMany
     */
    public function remedialRules()
    {
        return $this->hasMany(LegacyRemedialRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return HasOne
     */
    public function roundingTable()
    {
        return $this->belongsTo(LegacyRoundingTable::class, 'tabela_arredondamento_id');
    }

    /**
     * @return HasOne
     */
    public function conceptualRoundingTable()
    {
        return $this->belongsTo(LegacyRoundingTable::class, 'tabela_arredondamento_id_conceitual');
    }

    /**
     * @return BelongsTo
     */
    public function deficiencyEvaluationRule()
    {
        return $this->belongsTo(__CLASS__, 'regra_diferenciada_id');
    }

    /**
     * @return bool
     */
    public function isAverageBetweenScoreAndRemedialCalculation()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_AVERAGE_SCORE;
    }

    /**
     * @return bool
     */
    public function isSpecificRetake()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE;
    }

    /**
     * @return bool
     */
    public function isSumScoreCalculation()
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

    /**
     * @return bool
     */
    public function isGlobalScore()
    {
        return $this->nota_geral_por_etapa == 1;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome,
        );
    }

    public function gradeYears()
    {
        return $this->hasMany(LegacyEvaluationRuleGradeYear::class, 'regra_avaliacao_id');
    }

    public function averageFormula()
    {
        return $this->belongsTo(LegacyAverageFormula::class, 'formula_media_id');
    }

    public function recoveryFormula()
    {
        return $this->belongsTo(LegacyAverageFormula::class, 'formula_recuperacao_id');
    }
}
