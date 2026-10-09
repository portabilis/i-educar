<?php

use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyPeriod::class, relations: [
    'schoolClass' => LegacySchoolClass::class,
    'schoolClassTeacher' => LegacySchoolClassTeacher::class,
]));

EloquentModelTestCase::registerModelTests();

test('get name attribute', function () {
    $this->assertEquals($this->model->name, $this->model->nome);
});
