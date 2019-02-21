<?php

namespace Tests\Unit;

use iEducar\Modules\Config\IniConfig;
use Tests\TestCase;

class IniConfigTest extends TestCase
{
    public function testParsedIni()
    {
        $ini = new IniConfig(base_path() . '/tests/Fixtures/Config/ieducar.ini');
        $this->assertNotNull($ini->app);
    }

    public function testChangeEnviroment()
    {
        $ini = new IniConfig(base_path() . '/tests/Fixtures/Config/ieducar.ini');
        $this->assertEquals(false, (bool)$ini->php->display_errors);

        $ini->changeEnviroment('development');
        $this->assertEquals(true, (bool)$ini->php->display_errors);

        $ini->changeEnviroment('testing');
        $this->assertEquals(true, (bool)$ini->php->display_errors);

        $ini->changeEnviroment();
        $this->assertEquals(false, (bool)$ini->php->display_errors);
    }

    public function testInvalidIniFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Arquivo ini com problemas de sintaxe. Verifique a sintaxe arquivo');
        new IniConfig('/dev/null');
    }

    public function testSectionExtendsMoreThanOne()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível herdar mais que uma seção');
        new IniConfig(base_path() . '/tests/Fixtures/Config/ieducar-extends-broken.ini');
    }

    public function testIniSyntaxError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Arquivo ini com problemas de sintaxe. Verifique a sintaxe arquivo');
        new IniConfig(base_path() . '/tests/Fixtures/Config/ieducar-syntax-broken.ini');
    }

    public function testSectionInheritanceNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não foi possível estender staging, seção testing não existe');
        new IniConfig(base_path() . '/tests/Fixtures/Config/ieducar-inheritance-broken.ini');
    }
}