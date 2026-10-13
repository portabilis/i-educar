<?php

use App\Models\LegacyAccess;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyAccess::class));

EloquentModelTestCase::registerModelTests();

test('get last access', function () {
    $query = $this->model->getLastAccess();

    $this->assertNotNull($query);
    $this->assertInstanceOf(LegacyAccess::class, $query);
});
