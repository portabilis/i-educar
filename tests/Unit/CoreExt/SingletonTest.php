<?php

use PHPUnit\Framework\TestCase;


/**
 * CoreExt_SingletonTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Singleton
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_SingletonTest extends TestCase
{
    public function testInstanciaESingleton()
    {
        $instance1 = CoreExt_SingletonStub::getInstance();
        $oid1 = spl_object_hash($instance1);

        $instance2 = CoreExt_SingletonStub::getInstance();
        $oid2 = spl_object_hash($instance2);

        $this->assertSame($oid1, $oid2);
    }

    public function testClasseQueNaoImplementaMetodoLancaExcecao()
    {
        $this->expectException(\CoreExt_Exception::class);
        $instance1 = CoreExt_SingletonIncompleteStub::getInstance();
    }
}
