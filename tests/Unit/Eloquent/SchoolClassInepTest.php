<?php

use App\Models\LegacySchoolClass;
use App\Models\SchoolClassInep;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(SchoolClassInep::class, relations: [
    'schoolClass' => LegacySchoolClass::class,
]));

EloquentModelTestCase::registerModelTests();

test('number attribute', function () {
    $this->assertEquals($this->model->cod_turma_inep, $this->model->number);
});
