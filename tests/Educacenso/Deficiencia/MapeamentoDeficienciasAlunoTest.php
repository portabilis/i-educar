<?php

namespace Tests\Educacenso\Deficiencia;

use iEducar\Modules\Educacenso\Deficiencia\MapeamentoDeficienciasAluno;
use Tests\TestCase;

class MapeamentoDeficienciasAlunoTest extends TestCase
{
    public function test_mapeia_as_deficiencias_do_sistema_para_os_codigos_do_censo(): void
    {
        $mapeamento = MapeamentoDeficienciasAluno::getArrayMapeamentoDeficiencias();

        $this->assertSame(range(1, 13), array_keys($mapeamento));
        $this->assertSame(array_map('strval', range(17, 29)), array_values($mapeamento));
    }
}
