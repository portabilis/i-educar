<?php

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\ValueTurmaMaisEducacao;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    /**
     * @return ValueTurmaMaisEducacao
     */
    $this->getValueObject = function () {
        $valueObject = new ValueTurmaMaisEducacao;
        $valueObject->setDependenciaAdministrativa(DependenciaAdministrativaEscola::MUNICIPAL);
        $valueObject->setTipoAtendimento(TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR);
        $valueObject->setModalidade(1);
        $valueObject->setEtapaEnsino(4);
        $valueObject->setTipoMediacao(TipoMediacaoDidaticoPedagogico::PRESENCIAL);
        $valueObject->setTurmaMaisEducacao(1);

        return $valueObject;
    };
});

test('turma nao presencial value null', function () {
    $value = ($this->getValueObject)();
    $value->setTipoMediacao(2);
    $this->assertNull($value->getValue());
});

test('dependencia administrativa invalido value null', function () {
    $value = ($this->getValueObject)();
    $value->setDependenciaAdministrativa(1);
    $this->assertNull($value->getValue());
});

test('tipo atendimento invalido value null', function () {
    $value = ($this->getValueObject)();
    $value->setTipoAtendimento(TipoAtendimentoTurma::CLASSE_HOSPITALAR);
    $this->assertNull($value->getValue());
});

test('tipo atendimento valido modalidade invalida value null', function () {
    $value = ($this->getValueObject)();
    $value->setTipoAtendimento(3);
    $value->setModalidade(ModalidadeCurso::EJA);
    $this->assertNull($value->getValue());
});

test('tipo atendimento valido modalidade valida etapa ensino invalida value null', function () {
    $value = ($this->getValueObject)();
    $value->setTipoAtendimento(3);
    $value->setEtapaEnsino(3);
    $this->assertNull($value->getValue());
});

test('value nao nulo', function () {
    $value = ($this->getValueObject)();
    $this->assertEquals(1, $value->getValue());

    $value->setTurmaMaisEducacao(0);
    $this->assertEquals(0, $value->getValue());
});

test('turma curricular de etapa aceita fora da eja tem valor', function () {
    $value = ($this->getValueObject)();
    $value->setTipoAtendimento(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO);
    $value->setEtapaEnsino(14);

    $this->assertEquals(1, $value->getValue());
});
