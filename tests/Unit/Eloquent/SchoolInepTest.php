<?php

use App\Models\LegacySchool;
use App\Models\SchoolInep;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(SchoolInep::class, relations: [
    'school' => LegacySchool::class,
]));

EloquentModelTestCase::registerModelTests();

test('number attribute', function () {
    $this->assertEquals($this->model->cod_escola_inep, $this->model->number);
});
