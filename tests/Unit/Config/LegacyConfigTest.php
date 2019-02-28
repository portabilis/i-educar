<?php

namespace Tests\Unit;

use iEducar\Support\Config\LegacyConfig;
use Tests\TestCase;

class LegacyConfigTest extends TestCase
{
    public function getFixturePath()
    {
        return base_path() . '/tests/Fixtures/Config/';
    }
    public function testValidPathShouldLoadConfig()
    {
        $legacyConfigObject = new LegacyConfig(
            $this->getFixturePath()
        );

        $arrayConfig = $legacyConfigObject->getArrayConfig();
        $this->assertEquals('ieducardb', $arrayConfig['app']['database']['name']);
    }

    public function testValidPathAndEnviromentShouldLoadConfig()
    {
        $legacyConfigObject = new LegacyConfig(
            $this->getFixturePath(),
            'fake-enviroment'
        );

        $arrayConfig = $legacyConfigObject->getArrayConfig();
        $this->assertEquals('fakedatabase', $arrayConfig['app']['database']['name']);
    }

    public function testInvalidEnviromentShouldThrowException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Seção invalid-enviroment não encontrada no arquivo');
        new LegacyConfig(
            $this->getFixturePath(),
            'invalid-enviroment'
        );
    }

    public function testValidTenantShouldLoadConfig()
    {
        $legacyConfigObject = new LegacyConfig(
            $this->getFixturePath(),
            'production',
            'fake-tenant.ieducar.com.br'
        );

        $arrayConfig = $legacyConfigObject->getArrayConfig();
        $this->assertEquals('fake-tenant-database', $arrayConfig['app']['database']['name']);
    }
}