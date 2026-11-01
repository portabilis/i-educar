<?php

use App\Models\LegacyIndividualPicture;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyIndividualPicture::class));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->caminho, $this->model->url);

    $this->model->url = 'http://www.example.com';

    $this->assertEquals('http://www.example.com', $this->model->url);
});
