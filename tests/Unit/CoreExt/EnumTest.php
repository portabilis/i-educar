<?php

class CoreExt_EnumTest extends PHPUnit\Framework\TestCase
{
    public function test_retorna_todos_os_valores_do_enum()
    {
        $enum = CoreExt_Enum1Stub::getInstance();
        $this->assertEquals([1], $enum->getKeys());
        $enum = CoreExt_Enum2Stub::getInstance();
        $this->assertEquals([2], $enum->getKeys());
        $enum = CoreExt_EnumCoffeeStub::getInstance();
        $this->assertEquals([0, 1, 2], $enum->getKeys());
        $enum = CoreExt_EnumStringStub::getInstance();
        $this->assertEquals(['red'], $enum->getKeys());
    }

    public function test_item_de_enum_retorna_descricao()
    {
        $enum = CoreExt_Enum1Stub::getInstance();
        $this->assertEquals(1, $enum->getValue(CoreExt_Enum1Stub::ONE));
        $enum = CoreExt_Enum2Stub::getInstance();
        $this->assertEquals(2, $enum->getValue(CoreExt_Enum2Stub::TWO));
        $enum = CoreExt_EnumCoffeeStub::getInstance();
        $this->assertEquals('Mocha', $enum->getValue(CoreExt_EnumCoffeeStub::MOCHA));
        $enum = CoreExt_EnumStringStub::getInstance();
        $this->assertEquals('#FF0000', $enum->getValue(CoreExt_EnumStringStub::RED));
    }

    public function test_enum_acessados_como_array()
    {
        $enum = CoreExt_Enum1Stub::getInstance();
        $this->assertEquals(1, $enum[CoreExt_Enum1Stub::ONE]);
        $enum = CoreExt_Enum2Stub::getInstance();
        $this->assertEquals(2, $enum[CoreExt_Enum2Stub::TWO]);
        $enum = CoreExt_EnumCoffeeStub::getInstance();
        $this->assertEquals('Mocha', $enum[CoreExt_EnumCoffeeStub::MOCHA]);
        $enum = CoreExt_EnumStringStub::getInstance();
        $this->assertEquals('#FF0000', $enum[CoreExt_EnumStringStub::RED]);
    }

    public function test_enum_acessos_diversos_como_array()
    {
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
    }

    public function test_enum_e_apenas_leitura()
    {
        $this->expectException(\CoreExt_Exception::class);
        $enum = CoreExt_Enum1Stub::getInstance();
        $enum['foo'] = 'bar';
    }

    public function test_enum_nao_permite_remover_entrada()
    {
        $this->expectException(\CoreExt_Exception::class);
        $enum = CoreExt_Enum1Stub::getInstance();
        unset($enum['foo']);
    }
}
