<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro20;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Tests\TestCase;

class Registro20TipoAtendimentoTest extends TestCase
{
    public function test_helpers_reconhecem_codigo_combinado_9()
    {
        // 9 = Curricular (etapa de ensino) com Atividade Complementar
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['9']));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['9']));
        $this->assertFalse(TipoAtendimentoTurma::possuiAee(['9']));
    }

    public function test_helpers_tratam_legado_0_e_4_como_ambos()
    {
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['0', '4']));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['0', '4']));
    }

    public function test_helpers_codigos_individuais()
    {
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['0']));
        $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar(['0']));

        $this->assertFalse(TipoAtendimentoTurma::possuiCurricular(['4']));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['4']));

        $this->assertTrue(TipoAtendimentoTurma::possuiAee(['5']));
        $this->assertFalse(TipoAtendimentoTurma::possuiCurricular(['5']));
        $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar(['5']));
    }

    public function test_turma_codigo_9_eh_curricular_e_atividade_complementar()
    {
        $registro = new Registro20;
        $registro->tipoAtendimento = ['9'];

        // campos 15-20 dependem de atividadeComplementar();
        // campo 22 e componentes (39-65) dependem de curricularEtapaDeEnsino()
        $this->assertTrue($registro->atividadeComplementar());
        $this->assertTrue($registro->curricularEtapaDeEnsino());
        $this->assertFalse($registro->atendimentoEducacionalEspecializado());
    }

    public function test_tipo_turma_mapeia_codigo_9()
    {
        $registro = new Registro20;

        $registro->tipoAtendimento = ['9'];
        $this->assertSame(9, $registro->tipoTurma());

        // legado: 0 e 4 juntos equivalem ao 9
        $registro->tipoAtendimento = ['0', '4'];
        $this->assertSame(9, $registro->tipoTurma());

        $registro->tipoAtendimento = ['0'];
        $this->assertSame(6, $registro->tipoTurma());

        $registro->tipoAtendimento = ['4'];
        $this->assertSame(4, $registro->tipoTurma());

        $registro->tipoAtendimento = ['5'];
        $this->assertSame(5, $registro->tipoTurma());
    }
}
