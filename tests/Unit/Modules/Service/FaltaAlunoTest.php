<?php

use PHPUnit\Framework\MockObject\MockObject;

class Avaliacao_Service_FaltaAlunoTest extends Avaliacao_Service_TestCommon
{
    public function test_cria_nova_instancia_de_falta_aluno()
    {
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
        $this->assertEquals(1, $this->_getConfigOption('matricula', 'cod_matricula'));

        $this->_setFaltaAlunoDataMapperMock($mock);

        $_GET['etapa'] = 'Rc';

        $this->_getServiceInstance();
    }

    protected function tearDown(): void
    {
        $_GET = [];
        Portabilis_Utils_Database::$_db = null;
    }
}
