<?php

use App\Models\Enums\PersonLinkType;

test('descriptive values', function () {
    expect(PersonLinkType::getDescriptiveValues()->all())->toBe([
        'student' => 'Aluno',
        'employee' => 'Servidor',
        'mother' => 'Mãe da Pessoa Física',
        'father' => 'Pai da Pessoa Física',
        'responsible' => 'Responsável da Pessoa Física',
    ]);
});
