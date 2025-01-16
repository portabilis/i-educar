<?php

use Tests\TestCase;

class Core_Controller_Page_AbstractTest extends TestCase
{
    protected $_pageController = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_pageController = new Core_Controller_Page_AbstractStub;
    }

    public function test_opcao_de_configuracao_nao_existente_lanca_excecao()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $this->_pageController->setOptions(['foo' => 'bar']);
    }

    public function test_classe_data_mapper_e_gerada_a_partir_da_definicao_string()
    {
        $this->_pageController->_dataMapper = 'CoreExt_EntityDataMapperStub';
        $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir da instanciação de "Core_Page_Controller_Abstract".');

        $this->_pageController->setOptions(['datamapper' => 'CoreExt_EntityDataMapperStub']);
        $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setOptions()".');

        $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
        $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com nome de classe "CoreExt_DataMapper".');

        $this->_pageController->setDataMapper(new CoreExt_EntityDataMapperStub);
        $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com objeto "CoreExt_DataMapper".');
    }

    public function test_classe_data_mapper_nao_existente_lanca_excecao()
    {
        $this->expectException(\Core_Controller_Page_Exception::class);
        $this->_pageController->setDataMapper('FooDataMapper');
    }

    public function test_metodo_lanca_excecao_quando_nao_recebe_tipo_suportado()
    {
        $this->expectException(\CoreExt_Exception_InvalidArgumentException::class);
        $this->_pageController->setDataMapper(0);
    }

    public function test_classe_data_mapper_nao_informada_e_metodo_nao_subclassificado_lanca_excecao()
    {
        $this->expectException(\Core_Controller_Page_Exception::class);
        $this->_pageController->getDataMapper();
    }

    public function test_atribuicao_de_instancia_entity()
    {
        $this->_pageController->setEntity(new CoreExt_EntityStub);
        $this->assertInstanceOf('CoreExt_Entity', $this->_pageController->getEntity());
    }

    public function test_instancia_um_entity_caso_nenhuma_instancia_tenha_sido_atribuida_explicitamente()
    {
        $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
        $this->assertInstanceOf('CoreExt_Entity', $this->_pageController->getEntity());
    }

    public function test_numero_do_processo_configurado()
    {
        $this->_pageController->_processoAp = 1;
        $this->assertIsInt($this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
        $this->assertEquals(1, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

        $this->_pageController->setOptions(['processoAp' => 2]);
        $this->assertEquals(2, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setOptions()".');

        $this->_pageController->setBaseProcessoAp(3);
        $this->assertEquals(3, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setBaseProcessoAp()".');
    }

    public function test_numero_do_processo_nao_informado_e_metodo_nao_subclassificado_lanca_excecao()
    {
        $this->expectException(\Core_Controller_Page_Exception::class);
        $this->_pageController->getBaseProcessoAp();
    }

    public function test_titulo_configurado()
    {
        $this->_pageController->_titulo = 'foo';
        $this->assertIsString($this->_pageController->getBaseTitulo(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
        $this->assertEquals('foo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

        $this->_pageController->setOptions(['titulo' => 'bar']);
        $this->assertEquals('bar', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setOptions()".');

        $this->_pageController->setBaseTitulo('zoo');
        $this->assertEquals('zoo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setBaseTitulo()".');
    }

    public function test_titulo_nao_informado_e_metodo_nao_subclassificado_lanca_excecao()
    {
        $this->expectException(\Core_Controller_Page_Exception::class);
        $this->_pageController->getBaseTitulo();
    }

    public function test_append_output()
    {
        $this->_pageController->appendOutput('string 1')
            ->appendOutput('string 2');

        $this->assertEquals(
            'string 1' . PHP_EOL . 'string 2',
            $this->_pageController->getAppendedOutput(),
            '->getAppendedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
        );
    }

    public function test_get_apended_output_retorna_null_quando_nao_existe_conteudo_a_ser_adicionado()
    {
        $this->assertNull($this->_pageController->getAppendedOutput());
    }

    public function test_prepend_output()
    {
        $this->_pageController->prependOutput('string 1')
            ->prependOutput('string 2');

        $this->assertEquals(
            'string 1' . PHP_EOL . 'string 2',
            $this->_pageController->getPrependedOutput(),
            '->getPrependedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
        );
    }

    public function test_get_prepended_output_retorna_null_quando_nao_existe_conteudo_a_ser_adicionado()
    {
        $this->assertNull($this->_pageController->getPrependedOutput());
    }
}
