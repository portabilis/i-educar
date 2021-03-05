<?php


use PHPUnit\Framework\MockObject\MockObject;

/**
 * Avaliacao_Service_ParecerDescritivoAlunoTest class.
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
class Avaliacao_Service_ParecerDescritivoAlunoTest extends Avaliacao_Service_TestCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);
        parent::setUp();
    }

    public function testCriaNovaInstanciaDeParecerDescritivoAluno()
    {
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
            ->withConsecutive(
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]],
                [[], ['matricula' => $this->_getConfigOption('matricula', 'cod_matricula')]]
            )
            ->willReturnOnConsecutiveCalls([], [$parecerAluno]);

        $this->_setParecerDescritivoAlunoDataMapperMock($mock);

        $this->_getServiceInstance();
    }
}
