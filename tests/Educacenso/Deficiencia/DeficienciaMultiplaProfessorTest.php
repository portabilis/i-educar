<?php

namespace Tests\Educacenso\Deficiencia;

use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaProfessor;
use iEducar\Modules\Educacenso\Deficiencia\ValueDeficienciaMultipla;
use Tests\TestCase;

class DeficienciaMultiplaProfessorTest extends TestCase
{
    public function test_lista_os_pares_de_deficiencias_que_formam_deficiencia_multipla(): void
    {
        $combinacoes = (new DeficienciaMultiplaProfessor)->getCombinacoes();

        $this->assertCount(14, $combinacoes);
        $this->assertContains(['19', '24'], $combinacoes);
        $this->assertContains(['24', '25'], $combinacoes);
    }

    public function test_sem_deficiencias_o_valor_fica_vazio(): void
    {
        $this->assertNull((new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, []))->getValue());
    }

    public function test_um_par_da_lista_e_deficiencia_multipla(): void
    {
        $this->assertSame(1, (new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, ['20', '25']))->getValue());
    }

    public function test_deficiencias_que_nao_formam_par_nao_sao_deficiencia_multipla(): void
    {
        $this->assertSame(0, (new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor, ['19', '20']))->getValue());
    }
}
