<?php

use App\Exceptions\Unification\InactiveMainUnification;
use App\Models\Individual;
use App\Models\LogUnification;
use App\Models\Student;

test('a mensagem diz se o principal inativo é aluno ou pessoa', function () {
    $unification = fn (string $type) => (new LogUnification)->forceFill(['type' => $type]);

    expect((new InactiveMainUnification($unification(Student::class)))->getMessage())->toBe('O aluno está inativo ou foi unificado com outro aluno.')
        ->and((new InactiveMainUnification($unification(Individual::class)))->getMessage())->toBe('A pessoa está inativa ou foi unificada com outra pessoa.');
});
