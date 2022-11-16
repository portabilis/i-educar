<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolGradeDisciplineBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacySchoolGradeDiscipline
 *
 * @method static LegacySchoolGradeDisciplineBuilder query()
 */
class LegacySchoolGradeDiscipline extends Model
{
    use LegacyAttribute;

    protected $table = 'pmieducar.escola_serie_disciplina';

    public const CREATED_AT = null;

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = LegacySchoolGradeDisciplineBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'ref_cod_disciplina',
        'workload' => 'carga_horaria'
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
            get: fn () => $this->discipline?->name
        );
    }

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->carga_horaria
        );
    }

    /**
     * @return BelongsTo
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }
}
