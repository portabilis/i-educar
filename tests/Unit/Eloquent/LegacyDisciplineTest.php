<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentTestCase;

class LegacyDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'knowledgeArea' => LegacyKnowledgeArea::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDiscipline::class;
    }

    /** @test */
    public function getInstitutionIdAttribute()
    {
        $this->assertEquals($this->model->instituicao_id, $this->model->institution_id);
    }

    /** @test */
    public function getKnowledgeAreaIdAttribute()
    {
        $this->assertEquals($this->model->area_conhecimento_id, $this->model->knowledge_area_id);
    }

    /** @test */
    public function getNameAttribute()
    {
        $this->assertEquals($this->model->nome, $this->model->name);
    }

    /** @test */
    public function getAbbreviationAttribute()
    {
        $this->assertEquals($this->model->abreviatura, $this->model->abbreviation);
    }

    /** @test */
    public function getFoundationTypeAttribute()
    {
        $this->assertEquals($this->model->tipo_base, $this->model->foundation_type);
    }

    /** @test */
    public function getOrderAttribute()
    {
        $this->assertEquals($this->model->ordenamento, $this->model->order);
    }

    /** @test */
    public function getEducacensoCodeAttribute()
    {
        $this->assertEquals($this->model->codigo_educacenso, $this->model->educacenso_code);
    }
}
