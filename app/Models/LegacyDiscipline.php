<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 * @property string $name
 * @property int $instituicao_id
 * @property int $area_conhecimento_id
 * @property string $nome
 * @property string $abreviatura
 * @property string $tipo_base
 * @property int $ordenamento
 * @property int $codigo_educacenso
 */
class LegacyDiscipline extends LegacyModel
{
    public const CREATED_AT = null;

    protected $table = 'modules.componente_curricular';

    protected $fillable = [
        'instituicao_id',
        'area_conhecimento_id',
        'nome',
        'abreviatura',
        'tipo_base',
        'ordenamento',
        'codigo_educacenso',
        'desconsidera_para_progressao',
    ];

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'institution_id' => 'instituicao_id',
        'knowledge_area_id' => 'area_conhecimento_id',
        'name' => 'nome',
        'abbreviation' => 'abreviatura',
        'foundation_type' => 'tipo_base',
        'order' => 'ordenamento',
        'educacenso_code' => 'codigo_educacenso',
    ];

    protected function institutionId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->instituicao_id,
        );
    }

    /**
     * @return HasMany<LegacySchoolGradeDiscipline, $this>
     */
    public function schoolGradeDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolGradeDiscipline::class, 'ref_cod_disciplina');
    }

    protected function knowledgeAreaId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->area_conhecimento_id,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome,
        );
    }

    protected function abbreviation(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->abreviatura,
        );
    }

    protected function foundationType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo_base,
        );
    }

    protected function order(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ordenamento,
        );
    }

    protected function educacensoCode(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->codigo_educacenso,
        );
    }

    /**
     * @return BelongsTo<LegacyKnowledgeArea, $this>
     */
    public function knowledgeArea(): BelongsTo
    {
        return $this->belongsTo(LegacyKnowledgeArea::class, 'area_conhecimento_id');
    }
}
