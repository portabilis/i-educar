<?php

use PHPUnit\Framework\TestCase;

class CoreExt_SingletonTest extends TestCase
{
    public function test_instancia_e_singleton()
    {
        $instance1 = CoreExt_SingletonStub::getInstance();
        $oid1 = spl_object_hash($instance1);

        $instance2 = CoreExt_SingletonStub::getInstance();
        $oid2 = spl_object_hash($instance2);

        $this->assertSame($oid1, $oid2);
    }

    public function test_classe_que_nao_implementa_metodo_lanca_excecao()
    {
        $this->expectException(\CoreExt_Exception::class);
        $instance1 = CoreExt_SingletonIncompleteStub::getInstance();
    }
}
