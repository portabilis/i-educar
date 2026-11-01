<?php

use App\Models\LegacySchoolClass;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/school-class';
    $this->model = LegacySchoolClass::class;
    $this->factory = LegacySchoolClassFactory::class;
});

test('index', #[
    GET('/api/school-class', ['SchoolClass'], 'Get all school-classes'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'SchoolClass')
] function () {
    $this->index();
});

test('calendars of one school class', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'data_inicio' => '2030-02-01', 'data_fim' => '2030-06-30']);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2, 'data_inicio' => '2030-08-01', 'data_fim' => '2030-12-15']);

    $this->getJson('/api/school-class/calendars?ref_cod_turma=' . $schoolClass->getKey())
        ->assertOk()
        ->assertExactJson([['start_date' => '2030-02-01', 'end_date' => '2030-12-15']]);
});

test('calendars of eja school classes by filters', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
    $schoolClass->course->update(['modalidade_curso' => ModalidadeCurso::EJA]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'data_inicio' => '2030-02-01', 'data_fim' => '2030-12-15']);
    LegacySchoolClassFactory::new()->create(['ano' => 2030]);

    $this->getJson('/api/school-class/calendars?' . http_build_query([
        'ano' => 2030,
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
    ]))
        ->assertOk()
        ->assertExactJson([['start_date' => '2030-02-01', 'end_date' => '2030-12-15']]);
});

test('stages of a school class', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $schoolClass->course->update(['padrao_ano_escolar' => 0]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'dias_letivos' => 100]);

    $this->getJson('/api/school-class/stages/' . $schoolClass->getKey())
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['dias_letivos' => 100]);
});
