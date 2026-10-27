<?php

use App\Models\Exporter\Employee;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(Employee::class));

ViewModelTestCase::registerModelTests();

test('employee', function () {
    $found = Employee::query()->get();
    $this->assertCount(1, $found);
    $this->assertInstanceOf(Employee::class, $found->first());
    $this->assertJsonStringEqualsJsonString($this->model, $found->first());
});

test('get alias', function () {
    $aliases = collect($this->model->getExportedColumnsByGroup())->flatMap(fn ($item) => $item);
    $this->assertEquals('ID Pessoa', $aliases->get('id'));
});

test('get label', function () {
    $this->assertEquals('Servidores', $this->model->getLabel());
});

test('get description', function () {
    $this->assertEquals('Os dados exportados serão contabilizados por quantidade de servidores(as) alocados(as) no ano filtrado, agrupando as informações das alocações nas escolas.', $this->model->getDescription());
});

test('get exported columns by group', function () {
    $expected = [
        'Códigos' => [
            'id' => 'ID Pessoa',
            'school_id' => 'ID Escola',
        ],
        'Servidor' => [
            'name' => 'Nome',
            'social_name' => 'Nome social e/ou afetivo',
            'cpf' => 'CPF',
            'rg' => 'RG',
            'rg_issue_date' => 'RG (Data Emissão)',
            'rg_state_abbreviation' => 'RG (Estado)',
            'date_of_birth' => 'Data de nascimento',
            'email' => 'E-mail',
            'sus' => 'Número SUS',
            'nis' => 'NIS (PIS/PASEP)',
            'occupation' => 'Ocupação',
            'organization' => 'Empresa',
            'monthly_income' => 'Renda Mensal',
            'gender' => 'Gênero',
            'race' => 'Raça',
        ],
        'Alocação' => [
            'employee_workload' => 'Carga horária do servidor',
            'year' => 'Ano',
            'school' => 'Escola',
            'period' => 'Período',
            'role' => 'Função',
            'link' => 'Vínculo',
            'allocated_workload' => 'Carga horária alocada',
        ],
        'Informações' => [
            'phones.phones' => 'Telefones',
            'disabilities.disabilities' => 'Deficiências',
            'schooling_degree' => 'Escolaridade',
            'high_school_type' => 'Tipo de ensino médio cursado',
            'employee_postgraduates_complete' => 'Pós-Graduações concluídas',
            'continuing_education_course' => 'Outros cursos de formação continuada',
            'employee_graduation_complete' => 'Curso(s) superior(es) concluído(s)',
        ],
        'Endereço' => [
            'place.address' => 'Logradouro',
            'place.number' => 'Número',
            'place.complement' => 'Complemento',
            'place.neighborhood' => 'Bairro',
            'place.postal_code' => 'CEP',
            'place.latitude' => 'Latitude',
            'place.longitude' => 'Longitude',
            'place.city' => 'Cidade',
            'place.state_abbreviation' => 'Sigla do Estado',
            'place.state' => 'Estado',
            'place.country' => 'País',
        ],
    ];
    $this->assertJsonStringEqualsJsonString(collect($expected), collect($this->model->getExportedColumnsByGroup()));
});

test('relationship person', function () {
    $found = Employee::query()->person([
        'id',
        'name',
        'email',
    ])->first();
    $expected = [
        'person.id',
        'person.name',
        'person.email',
    ];
    $this->assertInstanceOf(Employee::class, $found);
    $this->assertEquals($expected, array_keys($found->getAttributes()));
});

test('relationship disabilities', function () {
    $found = Employee::query()->disabilities()->first();
    $this->assertInstanceOf(Employee::class, $found);
    $expected = [
        'Deficiências',
    ];
    $this->assertEquals($expected, array_keys($found->getAttributes()));
});

test('relationship phones', function () {
    $found = Employee::query()->phones()->first();
    $this->assertInstanceOf(Employee::class, $found);
    $expected = [
        'Telefones',
    ];
    $this->assertEquals($expected, array_keys($found->getAttributes()));
});

test('relationship place', function () {
    $found = Employee::query()->place([
        'address',
        'number',
        'complement',
        'neighborhood',
        'postal_code',
        'latitude',
        'longitude',
        'city',
        'state_abbreviation',
        'state',
        'country',
    ])->first();
    $this->assertInstanceOf(Employee::class, $found);
    $expected = [
        'Logradouro',
        'Número',
        'Complemento',
        'Bairro',
        'CEP',
        'Latitude',
        'Longitude',
        'Cidade',
        'Sigla do Estado',
        'Estado',
        'País',
    ];
    $this->assertEquals($expected, array_keys($found->getAttributes()));
});
