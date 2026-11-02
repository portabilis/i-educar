<?php

use App\Models\Gender;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new Gender)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Masculino',
        2 => 'Feminino',
    ]), collect($values));
});
