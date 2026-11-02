<?php

test('instancia e singleton', function () {
    $instance1 = CoreExt_SingletonStub::getInstance();
    $oid1 = spl_object_hash($instance1);

    $instance2 = CoreExt_SingletonStub::getInstance();
    $oid2 = spl_object_hash($instance2);

    $this->assertSame($oid1, $oid2);
});

test('classe que nao implementa metodo lanca excecao', function () {
    $this->expectException(CoreExt_Exception::class);
    $instance1 = CoreExt_SingletonIncompleteStub::getInstance();
});
