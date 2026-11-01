<?php

use App\Models\Nationality;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new Nationality)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Brasileira',
        2 => 'Naturalizado brasileiro',
        3 => 'Estrangeira',
    ]), collect($values));
});
