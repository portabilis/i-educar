<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use App\Models\LegacyUser;
use Database\Factories\LegacyStageTypeFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStageType::class, relations: [
    'institution' => LegacyInstitution::class,
    'academicYearStages' => LegacyAcademicYearStage::class,
    'schoolClassStage' => LegacySchoolClassStage::class,
    'createdByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $expected = sprintf('%s - %d etapa(s)', $this->model->nm_tipo, $this->model->num_etapas);
    $this->assertEquals($expected, $this->model->name);

    $expected = str_replace(["\r\n", "\r", "\n"], '<br />', $this->model->getRawOriginal('descricao'));
    $this->assertEquals($expected, $this->model->descricao);
});

test('scope active', function () {
    LegacyStageTypeFactory::new()->create(['ativo' => 0]);
    $found = $this->instanceNewEloquentModel()->active()->get();
    $this->assertCount(1, $found);
});

test('already exists', function () {
    $id = 1;
    $name = $this->model->nm_tipo;
    $stagesNumber = $this->model->num_etapas;
    $expected = $this->instanceNewEloquentModel()->query()->where('ativo', 1)
        ->where('nm_tipo', $name)
        ->where('num_etapas', $stagesNumber)
        ->when($id, static function ($query) use ($id) {
            $query->where('cod_modulo', '<>', $id);
        })
        ->exists();
    $this->assertEquals($expected, $this->instanceNewEloquentModel()->alreadyExists($name, $stagesNumber, $id));
});
