<?php

use App\Models\LegacyCourseEducacensoStage;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyCourseEducacensoStage::class));

EloquentModelTestCase::registerModelTests();

test('get ids by course', function () {
    $return = $this->model->getIdsByCourse($this->model->curso_id);
    $this->assertIsArray($return);
    $this->assertCount(1, $return);
});
