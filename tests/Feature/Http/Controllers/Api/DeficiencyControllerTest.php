<?php

use App\Models\LegacyDeficiency;
use Database\Factories\LegacyDeficiencyFactory;
use Tests\ResourceTestCase;

uses(ResourceTestCase::class);

beforeEach(function () {
    $this->uri = '/api/person/deficiency';
    $this->model = LegacyDeficiency::class;
    $this->factory = LegacyDeficiencyFactory::class;
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
