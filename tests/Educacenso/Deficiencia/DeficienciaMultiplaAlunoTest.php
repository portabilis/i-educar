<?php

namespace Tests\Educacenso\Deficiencia;

use iEducar\Modules\Educacenso\Deficiencia\CombinacaoDeficienciaMultipla;
use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaAluno;
use Tests\TestCase;

class DeficienciaMultiplaAlunoTest extends TestCase
{
    public function test_lista_os_pares_de_deficiencias_que_formam_deficiencia_multipla(): void
    {
        $combinacoes = (new DeficienciaMultiplaAluno)->getCombinacoes();

        $this->assertInstanceOf(CombinacaoDeficienciaMultipla::class, new DeficienciaMultiplaAluno);
        $this->assertCount(14, $combinacoes);
        $this->assertContains(['17', '22'], $combinacoes);
        $this->assertContains(['22', '23'], $combinacoes);
        $this->assertNotContains(['17', '18'], $combinacoes);

        foreach ($combinacoes as $par) {
            $this->assertCount(2, $par);
        }
    }
}
