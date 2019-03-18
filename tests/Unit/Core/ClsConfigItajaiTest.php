<?php

use Tests\TestCase;

require_once 'clsConfigItajai.inc.php';

class ClsConfigItajai extends TestCase
{
    protected $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = new clsConfig();
    }

    public function testConfigInstituicao()
    {
        $this->assertEquals('i-Educar - ', $this->config->_instituicao);
    }

    public function testArrayConfigHasEmailsAdministradores()
    {
        $this->assertTrue((bool) count($this->config->arrayConfig['ArrStrEmailsAdministradores']));
    }

    public function testArrayCheckEmailAdministradores()
    {
        $this->assertInternalType(
            'array',
            $this->config->arrayConfig['ArrStrEmailsAdministradores']
        );
    }

    public function testArrayConfigDirectoryTemplates()
    {
        $this->assertEquals('templates/', $this->config->arrayConfig['strDirTemplates']);
    }

    public function testArrayConfigIntSegundosQuerySql()
    {
        $this->assertEquals(3, $this->config->arrayConfig['intSegundosQuerySQL']);
    }

    public function testArrayConfigIntSegundosPagina()
    {
        $this->assertEquals(5, $this->config->arrayConfig['intSegundosProcessaPagina']);
    }
}
