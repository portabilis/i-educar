<?php

use PHPUnit\Framework\MockObject\MockObject;

uses(Avaliacao_Service_TestCommon::class);

test('cria nova instancia de falta aluno', function () {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltaSave = clone $faltaAluno;
    $faltaSave->id = null;

    // Configura mock para Avaliacao_Model_FaltaAlunoDataMapper
    /** @var MockObject|Avaliacao_Model_FaltaAlunoDataMapper $mock */
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaAlunoDataMapper');

    $mock
        ->method('save')
        ->with($faltaSave)
        ->willReturn(true);
    $mock
        ->expects(self::exactly(2))
        ->method('findAll')
        ->willReturnOnConsecutiveCalls([], [$faltaAluno]);
    $this->assertEquals($this->matricula->getKey(), $this->_getConfigOption('matricula', 'cod_matricula'));

    $this->_setFaltaAlunoDataMapperMock($mock);

    $_GET['etapa'] = 'Rc';

    $this->_getServiceInstance();
});
