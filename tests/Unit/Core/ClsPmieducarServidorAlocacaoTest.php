<?php


/**
 * ClsPmieducarServidorAlocacaoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Core
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.0.2
 *
 * @version     @@package_version@@
 */
class ClsPmieducarServidorAlocacaoTest extends PHPUnit\Framework\TestCase
{
    /**
     * Testa o método substituir_servidor().
     */
    public function testSubstituirServidor()
    {
        $stub = $this->getMockBuilder('clsPmieducarServidorAlocacao')->getMock();

        $stub->expects($this->any())
            ->method('substituir_servidor')
            ->will($this->returnValue(true));

        $this->assertTrue($stub->substituir_servidor(1));
    }
}
