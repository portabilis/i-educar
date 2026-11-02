<?php

use App\Models\Religion;
use Database\Factories\ReligionFactory;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/religion';
    $this->model = Religion::class;
    $this->factory = ReligionFactory::class;
});

test('index', function () {
    $this->index();
});

test('store', function () {
    $this->store();
});

test('show', function () {
    $this->show();
});

test('update', function () {
    $this->update();
});

test('delete', function () {
    $this->destroy();
});
