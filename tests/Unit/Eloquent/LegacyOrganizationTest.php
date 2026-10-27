<?php

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyOrganization::class, relations: [
    'person' => LegacyPerson::class,
]));

EloquentModelTestCase::registerModelTests();

test('get name attribute', function () {
    $this->assertEquals($this->model->name, $this->model->fantasia);
});
