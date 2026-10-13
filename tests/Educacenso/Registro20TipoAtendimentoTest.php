<?php

use App\Models\Educacenso\Registro20;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Tests\TestCase;

uses(TestCase::class);

test('helpers reconhecem codigo combinado 9', function () {
    // 9 = Curricular (etapa de ensino) com Atividade Complementar
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['9']));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['9']));
    $this->assertFalse(TipoAtendimentoTurma::possuiAee(['9']));
});

test('helpers tratam legado 0 e 4 como ambos', function () {
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['0', '4']));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['0', '4']));
});

test('helpers codigos individuais', function () {
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular(['0']));
    $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar(['0']));

    $this->assertFalse(TipoAtendimentoTurma::possuiCurricular(['4']));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar(['4']));

    $this->assertTrue(TipoAtendimentoTurma::possuiAee(['5']));
    $this->assertFalse(TipoAtendimentoTurma::possuiCurricular(['5']));
    $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar(['5']));
});

test('turma codigo 9 eh curricular e atividade complementar', function () {
    $registro = new Registro20;
    $registro->tipoAtendimento = ['9'];

    // campos 15-20 dependem de atividadeComplementar();
    // campo 22 e componentes (39-65) dependem de curricularEtapaDeEnsino()
    $this->assertTrue($registro->atividadeComplementar());
    $this->assertTrue($registro->curricularEtapaDeEnsino());
    $this->assertFalse($registro->atendimentoEducacionalEspecializado());
});

test('tipo turma mapeia codigo 9', function () {
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
});
