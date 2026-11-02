<?php

use App\Models\RegistryOrigin;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new RegistryOrigin)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Migração',
        2 => 'Cadastro',
        3 => 'Unificação',
        4 => 'Outro',
    ]), collect($values));
});
