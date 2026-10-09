<?php

use App\Models\LocalizationZone;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new LocalizationZone)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Urbana',
        2 => 'Rural',
    ]), collect($values));
});
