<?php

use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

test('possui curricular considera codigo proprio e combinado', function () {
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0]));
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([9]));
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0, 4])); // legado tratado como curricular + atividade
    $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([4]));
    $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([5]));
    $this->assertFalse(TipoAtendimentoTurma::possuiCurricular([]));
});

test('possui atividade complementar considera codigo proprio e combinado', function () {
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([4]));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([9]));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([0, 4])); // legado {0,4} reconhecido como tendo atividade
    $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([0]));
    $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([5]));
    $this->assertFalse(TipoAtendimentoTurma::possuiAtividadeComplementar([]));
});

test('legado curricular mais atividade classifica como tipo nove', function () {
    // {0,4} (Curricular + Atividade complementar separados) deve ser tratado como o código combinado 9.
    $this->assertTrue(TipoAtendimentoTurma::possuiCurricular([0, 4]));
    $this->assertTrue(TipoAtendimentoTurma::possuiAtividadeComplementar([0, 4]));
});
