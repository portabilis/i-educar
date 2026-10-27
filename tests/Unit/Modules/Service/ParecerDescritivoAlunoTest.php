<?php

use PHPUnit\Framework\MockObject\MockObject;

uses(Avaliacao_Service_TestCommon::class);

beforeEach(function () {
    $this->configuraRegra(['parecerDescritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE]);
});

test('cria nova instancia de parecer descritivo aluno', function () {
    $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

    $parecerSave = clone $parecerAluno;
    $parecerSave->id = null;

    // Configura mock para Avaliacao_Model_ParecerDescritivoAlunoDataMapper
    /** @var Avaliacao_Model_ParecerDescritivoAlunoDataMapper|MockObject $mock */
    $mock = $this->getCleanMock('Avaliacao_Model_ParecerDescritivoAlunoDataMapper');

    $mock
        ->method('save')
        ->with($parecerSave)
        ->willReturn(true);

    $mock
        ->expects(self::exactly(2))
        ->method('findAll')
        ->willReturnOnConsecutiveCalls([], [$parecerAluno]);
    $this->assertEquals($this->matricula->getKey(), $this->_getConfigOption('matricula', 'cod_matricula'));

    $this->_setParecerDescritivoAlunoDataMapperMock($mock);

    $this->_getServiceInstance();
});
