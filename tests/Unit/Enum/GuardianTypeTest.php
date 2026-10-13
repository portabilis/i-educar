<?php

use App\Models\GuardianType;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new GuardianType)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Pai',
        2 => 'Mãe',
        3 => 'Pai e Mãe',
        4 => 'Outra pessoa',
    ]), collect($values));
});
