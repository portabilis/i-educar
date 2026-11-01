<?php

use App\Models\GuardianType;
use App\Models\Individual;
use App\Models\LogUnification;
use App\Models\Person;
use App\Models\Registration;
use App\Models\Religion;
use App\Models\Student;
use App\Models\TransportationProvider;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(Student::class, relations: [
    'individual' => Individual::class,
    'person' => Person::class,
    'religion' => Religion::class,
    'registrations' => Registration::class,
    'createdBy' => Individual::class,
    'deletedBy' => Individual::class,
    'unification' => LogUnification::class,
]));

ViewModelTestCase::registerModelTests();

test('get guardian type attribute', function () {
    $this->assertEquals(3, $this->model->guardian_type);
});

test('get guardian type father attribute', function () {
    $student = $this->factory->father()->make();

    $this->assertEquals(1, $student->guardian_type);
});

test('get guardian type mother attribute', function () {
    $student = $this->factory->mother()->make();

    $this->assertEquals(2, $student->guardian_type);
});

test('get guardian type other attribute', function () {
    $student = $this->factory->guardian()->make();

    $this->assertEquals(4, $student->guardian_type);
});

test('get null guardian type attribute', function () {
    $student = $this->factory->noGuardian()->make();

    $this->assertNull($student->guardian_type);
});

test('get guardian type description attribute', function () {
    $expected = (new GuardianType)->getDescriptiveValues()[(int) $this->model->guardian_type];
    $this->assertNotNull($this->model->guardian_type_description);
    $this->assertEquals($expected, $this->model->guardian_type_description);
});

test('get transportation provider description attribute', function () {
    $expected = (new TransportationProvider)->getDescriptiveValues()[(int) $this->model->transportation_provider];

    $this->assertNotNull($this->model->transportation_provider_description);
    $this->assertEquals($expected, $this->model->transportation_provider_description);
});

test('get transportation vehicle type description attribute', function () {
    $this->assertNull($this->model->transportation_vehicle_type_description);
    $this->assertEquals(0, $this->model->transportation_vehicle_type_description);
});
