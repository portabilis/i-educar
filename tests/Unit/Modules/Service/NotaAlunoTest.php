<?php

use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/TestCommon.php';
require_once 'Avaliacao/Model/NotaComponente.php';

uses(Avaliacao_Service_TestCommon::class);

test('cria nova instancia de nota aluno', function () {
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');
    $notaSave = clone $notaAluno;
    $notaSave->id = null;

    // Configura mock para Avaliacao_Model_NotaAlunoDataMapper
    /** @var Avaliacao_Model_NotaAlunoDataMapper|MockObject $mock */
    $mock = $this->getCleanMock('Avaliacao_Model_NotaAlunoDataMapper');

    $mock
        ->method('save')
        ->with($notaSave)
        ->willReturn(true);
    $mock
        ->expects(self::exactly(2))
        ->method('findAll')
        ->willReturnOnConsecutiveCalls([], [$notaAluno]);

    $this->assertEquals($this->matricula->getKey(), $this->_getConfigOption('matricula', 'cod_matricula'));

    $this->_setNotaAlunoDataMapperMock($mock);

    $this->_getServiceInstance();
});
