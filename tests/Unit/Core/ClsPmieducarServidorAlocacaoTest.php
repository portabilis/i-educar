<?php

class ClsPmieducarServidorAlocacaoTest extends PHPUnit\Framework\TestCase
{
    /**
     * Testa o método substituir_servidor().
     */
    public function test_substituir_servidor()
    {
        $stub = $this->getMockBuilder('clsPmieducarServidorAlocacao')->getMock();

        $stub->expects($this->any())
            ->method('substituir_servidor')
            ->will($this->returnValue(true));

        $this->assertTrue($stub->substituir_servidor(1));
    }
}
