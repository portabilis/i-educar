<?php

use App\Models\Exporter\SocialAssistance;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(SocialAssistance::class));

// O teste comum 'find using eloquent' é sobrescrito aqui, por isso os testes
// comuns não são registrados com ViewModelTestCase::registerModelTests().
test('find using eloquent', function () {
    $this->assertTrue(true);
});

test('relationships', function () {
    // Como o teste comum, sem relações configuradas.
    $this->assertTrue(true);
});

test('social assistance', function () {
    $found = SocialAssistance::query()->where($this->model->getAttributes())->get();
    $this->assertInstanceOf(SocialAssistance::class, $found->first());
    $this->assertJsonStringEqualsJsonString($this->model, $found->first());
});

test('get alias', function () {
    $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
    $this->assertEquals('Nome', $aliases->get('name'));
});

test('get label', function () {
    $this->assertEquals('Dados de escolaridade - Assistência Social', $this->model->getLabel());
});

test('get description', function () {
    $this->assertEquals('Os dados exportados serão contabilizados por quantidade de matrículas dos(as) alunos(as), duplicando o(a) aluno(a) caso o mesmo possua mais de uma matrícula no ano filtrado. Opção utilizada para integração com sistemas de Assistência social que coletem dados de escolaridade das famílias atendidas.', $this->model->getDescription());
});

test('get exported columns by group', function () {
    $expected = [
        'Aluno' => [
            'name' => 'Nome',
            'cpf' => 'CPF',
            'date_of_birth' => 'Data de nascimento',
            'nis' => 'NIS (PIS/PASEP)',
        ],
        'Escola' => [
            'school' => 'Escola',
            'school_inep' => 'Código INEP',
            'period' => 'Turno',
            'school_class_stage' => 'Etapa Educacenso',
            'attendance_type' => 'Tipo da turma',
        ],
    ];
    $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
});
