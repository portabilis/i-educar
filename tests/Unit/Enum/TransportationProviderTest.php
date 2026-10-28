<?php

use App\Models\TransportationProvider;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->enum = new TransportationProvider;
});

test('descriptive values', function () {
    $values = (new TransportationProvider)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        0 => 'Não utiliza',
        1 => 'Estadual',
        2 => 'Municipal',
    ]), collect($values));
});

test('from', function () {
    $this->assertEquals(0, $this->enum->from(null));
    $this->assertEquals(1, $this->enum->from('estadual'));
    $this->assertEquals(2, $this->enum->from('municipal'));
});

test('value description', function () {
    $this->assertEquals('nenhum', $this->enum->getValueDescription(0));
    $this->assertEquals('estadual', $this->enum->getValueDescription(1));
    $this->assertEquals('municipal', $this->enum->getValueDescription(2));
});
