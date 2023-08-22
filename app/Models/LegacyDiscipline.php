<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyDiscipline extends LegacyModel
{
    public const CREATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'modules.componente_curricular';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
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
     * @return BelongsTo
     */
    public function knowledgeArea()
    {
        return $this->belongsTo(LegacyKnowledgeArea::class, 'area_conhecimento_id');
    }
}
