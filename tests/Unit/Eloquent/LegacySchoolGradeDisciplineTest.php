<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacySchoolGradeDiscipline::class,
    relations: [
        'discipline' => LegacyDiscipline::class,
    ],
    legacyAttributes: [
        'id' => 'ref_cod_disciplina',
        'workload' => 'carga_horaria',
    ],
    factoryModifier: function (LegacySchoolGradeDisciplineFactory $factory) {
        return $factory->withLegacyDefinition();
    },
));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->discipline->name ?? null, $this->model->name);
    $this->assertEquals($this->model->carga_horaria, $this->model->workload);
});

test('chave primaria identifica o vinculo e nao o componente', function () {
    $this->assertEquals('id', $this->model->getKeyName());
    $this->assertEquals($this->model->getAttributes()['id'], $this->model->getKey());
    $this->assertNotEquals('ref_cod_disciplina', $this->model->getKeyName());
});
