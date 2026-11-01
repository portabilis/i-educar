<?php

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolAcademicYear::class, relations: [
    'school' => LegacySchool::class,
]));

EloquentModelTestCase::registerModelTests();

test('year attribute', function () {
    $this->assertEquals($this->model->ano, $this->model->year);
});
