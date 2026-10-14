<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDiscipline::class, relations: [
    'knowledgeArea' => LegacyKnowledgeArea::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->instituicao_id, $this->model->institution_id);
    $this->assertEquals($this->model->area_conhecimento_id, $this->model->knowledge_area_id);
    $this->assertEquals($this->model->nome, $this->model->name);
    $this->assertEquals($this->model->abreviatura, $this->model->abbreviation);
    $this->assertEquals($this->model->tipo_base, $this->model->foundation_type);
    $this->assertEquals($this->model->ordenamento, $this->model->order);
    $this->assertEquals($this->model->codigo_educacenso, $this->model->educacenso_code);
});
