<?php

use App\Models\LegacySchool;
use App\Models\View\SchoolData;
use Database\Factories\SchoolDataFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('create reads from view', function () {
    $schoolData = SchoolDataFactory::new()->create([
        'nome' => 'Escola Municipal Içara',
        'logradouro' => 'Rua Marechal Deodoro',
        'numero' => '1200',
        'bairro' => 'Jardim Elizabete',
        'cep' => '88820000',
        'municipio' => 'Içara',
        'uf_municipio' => 'SC',
        'inep' => '42000001',
        'telefone_ddd' => '48',
        'telefone' => '3432-1234',
        'celular_ddd' => '48',
        'celular' => '99123-4567',
    ]);

    $this->assertTrue($schoolData->exists);
    $this->assertEquals($schoolData->getAttributes(), SchoolData::query()->find($schoolData->getKey())->getAttributes());
    $this->assertEquals('ESCOLA MUNICIPAL IÇARA', $schoolData->name);
    $this->assertEquals('Rua Marechal Deodoro, Nº.: 1200, Jardim Elizabete - Içara - SC - CEP: 88820-000', $schoolData->address);
    $this->assertEquals('42000001', $schoolData->inep);
    $this->assertEquals('(48) 3432-1234', $schoolData->phone);
    $this->assertEquals('(48) 99123-4567', $schoolData->cellphone);
});

test('school relation', function () {
    $schoolData = SchoolDataFactory::new()->create();

    $this->assertEquals($schoolData->getAttributes(), LegacySchool::query()->find($schoolData->getKey())->data->getAttributes());
});

test('without number', function () {
    $schoolData = SchoolDataFactory::new()->withoutNumber()->create();

    $this->assertNull($schoolData->numero);
    $this->assertStringContainsString(', S/N, ', $schoolData->address);
});

test('without phones', function () {
    $schoolData = SchoolDataFactory::new()->withoutPhone()->withoutCellphone()->create();

    $this->assertEquals('(##) ####-####', $schoolData->phone);
    $this->assertEquals('(##) #####-####', $schoolData->cellphone);
});

test('without address and inep', function () {
    $schoolData = SchoolDataFactory::new()->withoutAddress()->withoutInep()->create();

    $this->assertNotNull($schoolData->nome);
    $this->assertNull($schoolData->logradouro);
    $this->assertNull($schoolData->municipio);
    $this->assertNull($schoolData->cep);
    $this->assertNull($schoolData->inep);
});

test('make does not touch database', function () {
    $schoolData = SchoolDataFactory::new()->make(['municipio' => 'Içara']);

    $this->assertFalse($schoolData->exists);
    $this->assertEquals('Içara', $schoolData->municipio);
});
