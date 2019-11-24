<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDiscipline extends Model
{
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
        'instituicao_id', 'area_conhecimento_id', 'nome', 'abreviatura', 'tipo_base', 'ordenamento', 'codigo_educacenso',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function getInstitutionIdAttribute()
    {
        return $this->instituicao_id;
    }

    public function getKnowledgeAreaIdAttribute()
    {
        return $this->area_conhecimento_id;
    }

    public function getNameAttribute()
    {
        return $this->nome;
    }

    public function getAbbreviationAttribute()
    {
        return $this->abreviatura;
    }

    public function getFoundationTypeAttribute()
    {
        return $this->tipo_base;
    }

    public function getOrderAttribute()
    {
        return $this->ordenamento;
    }

    public function getEducacensoCodeAttribute()
    {
        return $this->codigo_educacenso;
    }

    /**
     * @return BelongsTo
     */
    public function knowledgeArea()
    {
        return $this->belongsTo(LegacyKnowledgeArea::class, 'area_conhecimento_id');
    }
}
