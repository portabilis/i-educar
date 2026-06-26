<?php

namespace Tests\Unit\Modules\Educacenso\Model;

use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use PHPUnit\Framework\TestCase;

class TipoAtendimentoTurmaTest extends TestCase
{
    public function test_possui_curricular_considera_codigo_proprio_e_combinado(): void
    {
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0]));
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([9]));
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0, 4])); // legado tratado como curricular + atividade
        $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([4]));
        $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([5]));
        $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([]));
    }

    public function test_possui_atividade_complementar_considera_codigo_proprio_e_combinado(): void
    {
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([4]));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([9]));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([0, 4])); // legado {0,4} reconhecido como tendo atividade
        $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([0]));
        $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([5]));
        $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([]));
    }

    public function test_legado_curricular_mais_atividade_classifica_como_tipo_nove(): void
    {
        // {0,4} (Curricular + Atividade complementar separados) deve ser tratado como o código combinado 9.
        $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0, 4]));
        $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([0, 4]));
    }
}
