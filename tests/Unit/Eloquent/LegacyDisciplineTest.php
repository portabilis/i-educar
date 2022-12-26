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
    public function attributes()
    {
        $this->assertEquals($this->model->instituicao_id, $this->model->institution_id);
        $this->assertEquals($this->model->area_conhecimento_id, $this->model->knowledge_area_id);
        $this->assertEquals($this->model->nome, $this->model->name);
        $this->assertEquals($this->model->abreviatura, $this->model->abbreviation);
        $this->assertEquals($this->model->tipo_base, $this->model->foundation_type);
        $this->assertEquals($this->model->ordenamento, $this->model->order);
        $this->assertEquals($this->model->codigo_educacenso, $this->model->educacenso_code);
    }
}
