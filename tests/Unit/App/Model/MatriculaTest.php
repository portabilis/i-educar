<?php


/**
 * App_Model_MatriculaTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     App_Model
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class App_Model_MatriculaTest extends UnitBaseTest
{
    public function testAtualizaMatricula()
    {
        $matricula = $this->getCleanMock('clsPmieducarMatricula');
        $matricula->expects($this->once())
            ->method('edita')
            ->will($this->returnValue(true));

        // Guarda no repositório estático de classes
        CoreExt_Entity::addClassToStorage(
            'clsPmieducarMatricula',
            $matricula,
            null,
            true
        );

        App_Model_Matricula::atualizaMatricula(1, 1, true);
    }
}
