<?php


use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/TestCommon.php';
require_once 'Avaliacao/Model/NotaComponente.php';

/**
 * Avaliacao_Service_NotaAlunoTest class.
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
class Avaliacao_Service_NotaAlunoTest extends Avaliacao_Service_TestCommon
{
    public function testCriaNovaInstanciaDeNotaAluno()
    {
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
            ->withConsecutive(
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]],
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]]
            )
            ->willReturnOnConsecutiveCalls([], [$notaAluno]);

        $this->_setNotaAlunoDataMapperMock($mock);

        $this->_getServiceInstance();
    }

    public function tearDown(): void
    {
        Portabilis_Utils_Database::$_db = null;
    }
}
