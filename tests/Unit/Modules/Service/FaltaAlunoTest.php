<?php

use PHPUnit\Framework\MockObject\MockObject;

/**
 * Avaliacao_Service_FaltaAlunoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaAlunoTest extends Avaliacao_Service_TestCommon
{
    public function testCriaNovaInstanciaDeFaltaAluno()
    {
        $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

        $faltaSave  = clone $faltaAluno;
        $faltaSave->id = null;

        // Configura mock para Avaliacao_Model_FaltaAlunoDataMapper
        /** @var MockObject|Avaliacao_Model_FaltaAlunoDataMapper  $mock */
        $mock = $this->getCleanMock('Avaliacao_Model_FaltaAlunoDataMapper');

        $mock
            ->method('save')
            ->with($faltaSave)
            ->willReturn(true);

        $mock
            ->expects(self::exactly(2))
            ->method('findAll')
            ->withConsecutive(
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]],
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]]
            )
            ->willReturnOnConsecutiveCalls([], [$faltaAluno]);

        $this->_setFaltaAlunoDataMapperMock($mock);

        $_GET['etapa'] = 'Rc';

        $this->_getServiceInstance();
    }

    public function tearDown(): void
    {
        $_GET = [];
        Portabilis_Utils_Database::$_db = null;
    }
}
