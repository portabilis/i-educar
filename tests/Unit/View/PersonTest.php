<?php

use App\Models\Individual;
use App\Models\Person;
use App\Models\PersonType;
use App\Models\Place;
use App\Models\RegistryOrigin;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(Person::class, relations: [
    'individual' => Individual::class,
    'createdBy' => Individual::class,
    'updatedBy' => Individual::class,
    'place' => Place::class,
]));

ViewModelTestCase::registerModelTests();

test('type description', function () {
    $expected = (new PersonType)->getDescriptiveValues()[(int) $this->model->type];
    $this->assertEquals($expected, $this->model->typeDescription);
});

test('registry origin description', function () {
    $expected = (new RegistryOrigin)->getDescriptiveValues()[(int) $this->model->registry_origin];
    $this->assertEquals($expected, $this->model->registryOriginDescription);
});
