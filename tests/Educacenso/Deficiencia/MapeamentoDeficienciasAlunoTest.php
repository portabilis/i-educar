<?php

use iEducar\Modules\Educacenso\Deficiencia\MapeamentoDeficienciasAluno;
use Tests\TestCase;

uses(TestCase::class);

test('mapeia as deficiencias do sistema para os codigos do censo', function () {
    $mapeamento = MapeamentoDeficienciasAluno::getArrayMapeamentoDeficiencias();

    $this->assertSame(range(1, 13), array_keys($mapeamento));
    $this->assertSame(array_map('strval', range(17, 29)), array_values($mapeamento));
});
