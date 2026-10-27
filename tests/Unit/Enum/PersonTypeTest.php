<?php

use App\Models\PersonType;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new PersonType)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Pessoa Física',
        2 => 'Pessoa Jurídica',
    ]), collect($values));
});
