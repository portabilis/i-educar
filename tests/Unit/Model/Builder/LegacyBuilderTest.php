<?php

use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // escola 1 para ser excluído dos filtros
    $this->schoolNotInstitution = LegacySchoolFactory::new()->create();

    // instituição 1 para ser adicionado nos filtros
    $institution = LegacyInstitutionFactory::new()->create();

    // escola 2 para ser adicionado nos filtros
    $this->school = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $institution->id,
    ]);

    // builder para teste
    $this->builder = $this->school->query();
});

test('builder return with alias x expect resource', function () {
    $filtered = $this->builder->filter(['institution' => $this->school->ref_cod_instituicao])->setExcept(['ref_idpes'])->resource(['id', 'id as value'], ['name', 'name as label']);

    $expect = new Collection([
        [
            'id' => $this->school->id, // teste query sem alias
            'value' => $this->school->id, // teste query com alias
            'name' => $this->school->name, // teste adicional sem alias
            'label' => $this->school->name, // teste adicional com alias
        ],
    ]);

    $this->assertJsonStringEqualsJsonString($expect->toJson(), $filtered->toJson());
});

test('builder return expect empty resource', function () {
    $filtered = $this->builder->filter(['institution' => 0])->setExcept(['ref_idpes'])->resource(['id'], ['name']);

    $this->assertCount(0, $filtered);
});

test('builder select array', function () {
    $school = $this->builder->select(['sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao'])->where('cod_escola', $this->school->id)->first();

    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});

test('builder select string', function () {
    $school = $this->builder->select('sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao')->where('cod_escola', $this->school->id)->first();

    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});

test('builder get array', function () {
    $school = $this->builder->where('cod_escola', $this->school->id)->get(['sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao']);

    $school = $school->first();
    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});

test('builder get string', function () {
    $school = $this->builder->where('cod_escola', $this->school->id)->get('sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao');

    $school = $school->first();
    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});

test('builder first array', function () {
    $school = $this->builder->where('cod_escola', $this->school->id)->first(['sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao']);

    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});

test('builder first string', function () {
    $school = $this->builder->where('cod_escola', $this->school->id)->first('sigla', 'orgao_vinculado_escola', 'esfera_administrativa', 'unidade_vinculada_outra_instituicao');

    $this->assertArrayHasKey('sigla', $school->toArray());
    $this->assertArrayHasKey('orgao_vinculado_escola', $school->toArray());
    $this->assertArrayHasKey('esfera_administrativa', $school->toArray());
    $this->assertArrayHasKey('unidade_vinculada_outra_instituicao', $school->toArray());
});
