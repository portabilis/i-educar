<?php

test('retorna todos os valores do enum', function () {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals([1], $enum->getKeys());
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals([2], $enum->getKeys());
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals([0, 1, 2], $enum->getKeys());
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals(['red'], $enum->getKeys());
});

test('item de enum retorna descricao', function () {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum->getValue(CoreExt_Enum1Stub::ONE));
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum->getValue(CoreExt_Enum2Stub::TWO));
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum->getValue(CoreExt_EnumCoffeeStub::MOCHA));
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum->getValue(CoreExt_EnumStringStub::RED));
});

test('enum acessados como array', function () {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum[CoreExt_Enum1Stub::ONE]);
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum[CoreExt_Enum2Stub::TWO]);
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum[CoreExt_EnumCoffeeStub::MOCHA]);
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum[CoreExt_EnumStringStub::RED]);
});

test('enum acessos diversos como array', function () {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_Enum1Stub::ONE]));

    $this->assertEquals([1], $enum->getValues());
    $this->assertEquals([1], $enum->getKeys());
    $this->assertEquals([1 => 1], $enum->getEnums());
    $this->assertEquals(1, $enum->getKey(CoreExt_Enum1Stub::ONE));

    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_EnumStringStub::RED]));

    $this->assertEquals(['#FF0000'], $enum->getValues());
    $this->assertEquals(['red'], $enum->getKeys());
    $this->assertEquals(['red' => '#FF0000'], $enum->getEnums());
    $this->assertEquals('red', $enum->getKey('#FF0000'));
});

test('enum e apenas leitura', function () {
    $this->expectException(CoreExt_Exception::class);
    $enum = CoreExt_Enum1Stub::getInstance();
    $enum['foo'] = 'bar';
});

test('enum nao permite remover entrada', function () {
    $this->expectException(CoreExt_Exception::class);
    $enum = CoreExt_Enum1Stub::getInstance();
    unset($enum['foo']);
});
