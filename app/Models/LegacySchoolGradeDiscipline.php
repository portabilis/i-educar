<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolGradeDisciplineBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacySchoolGradeDiscipline
 *
 * @method static LegacySchoolGradeDisciplineBuilder query()
 *
 * @property LegacyDiscipline $discipline
 * @property int $ref_cod_disciplina
 * @property int $carga_horaria
 */
class LegacySchoolGradeDiscipline extends Model
{
    /** @use HasBuilder<LegacySchoolGradeDisciplineBuilder> */
    use HasBuilder;

    protected $table = 'pmieducar.escola_serie_disciplina';

    public const CREATED_AT = null;

    /**
     * Builder dos filtros
     */
    protected static string $builder = LegacySchoolGradeDisciplineBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'ref_cod_disciplina',
        'workload' => 'carga_horaria',
    ];

    protected $primaryKey = 'ref_cod_disciplina';

    protected $fillable = [
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_disciplina',
        'ativo',
        'carga_horaria',
        'etapas_especificas',
        'etapas_utilizadas',
        'updated_at',
        'anos_letivos',
        'hora_falta',
        'aulas_por_semana',
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_disciplina
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->discipline->name ?? null
        );
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria
        );
    }

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }

    /**
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }
}
