<?php

use App\Models\Employee;
use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use iEducar\Modules\Servidores\Model\TipoVinculo;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolClassTeacher::class, relations: [
    'schoolClass' => LegacySchoolClass::class,
    'schoolClassTeacherDisciplines' => LegacySchoolClassTeacherDiscipline::class,
    'period' => LegacyPeriod::class,
]));

EloquentModelTestCase::registerModelTests();

test('link type name', function () {
    $names = [TipoVinculo::EFETIVO => 'Efetivo', TipoVinculo::TEMPORARIO => 'Temporário', TipoVinculo::TERCEIRIZADO => 'Terceirizado', TipoVinculo::CLT => 'CLT', 99 => null];

    foreach ($names as $type => $name) {
        $this->model->tipo_vinculo = $type;

        $this->assertSame($name, $this->model->linkTypeName);
    }
});

test('employee relation', function () {
    $this->assertInstanceOf(Employee::class, $this->model->employee);
});
