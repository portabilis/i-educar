<?php

use App\Models\Employee;
use App\Models\EmployeeInep;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeeInep::class, relations: [
    'employee' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();

test('get number attribute', function () {
    $this->assertEquals($this->model->cod_docente_inep, $this->model->number);
});
