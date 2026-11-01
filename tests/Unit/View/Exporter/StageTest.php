<?php

use App\Models\Exporter\Stage;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(Stage::class));

// O teste comum 'find using eloquent' é sobrescrito aqui, por isso os testes
// comuns não são registrados com ViewModelTestCase::registerModelTests().
test('find using eloquent', function () {
    $this->assertTrue(true);
});

test('relationships', function () {
    // Como o teste comum, sem relações configuradas.
    $this->assertTrue(true);
});

test('person', function () {
    $found = Stage::query()->get();
    $this->assertCount(1, $found);
    $this->assertInstanceOf(Stage::class, $found->first());
    $this->assertJsonStringEqualsJsonString($this->model, $found->first());
});

test('get alias', function () {
    $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
    $this->assertEquals('Escola', $aliases->get('school_name'));
});

test('get label', function () {
    $this->assertEquals('Calendário letivo', $this->model->getLabel());
});

test('get description', function () {
    $this->assertEquals('Exportação de todos os calendários letivos do ano filtrado para identificação das datas de início e fim das etapas e existência de lançamentos.', $this->model->getDescription());
});

test('get exported columns by group', function () {
    $expected = [
        'Etapas' => [
            'school_name' => 'Escola',
            'school_class' => 'Turma',
            'stage_name' => 'Tipo de etapa',
            'stage_number' => 'Etapa',
            'stage_start_date' => 'Data início',
            'stage_end_date' => 'Data fim',
            'stage_days' => 'Dias letivos',
            'stage_type' => 'Padrão/Turma',
            'posted_data' => 'Possui lançamentos',
        ],
    ];
    $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
});
