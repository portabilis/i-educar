<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->_pageController = new Core_Controller_Page_AbstractStub;
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $this->_pageController->setOptions(['foo' => 'bar']);
});

test('classe data mapper e gerada a partir da definicao string', function () {
    $this->_pageController->_dataMapper = 'CoreExt_EntityDataMapperStub';
    $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(['datamapper' => 'CoreExt_EntityDataMapperStub']);
    $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com nome de classe "CoreExt_DataMapper".');

    $this->_pageController->setDataMapper(new CoreExt_EntityDataMapperStub);
    $this->assertInstanceOf('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com objeto "CoreExt_DataMapper".');
});

test('classe data mapper nao existente lanca excecao', function () {
    $this->expectException(Core_Controller_Page_Exception::class);
    $this->_pageController->setDataMapper('FooDataMapper');
});

test('metodo lanca excecao quando nao recebe tipo suportado', function () {
    $this->expectException(CoreExt_Exception_InvalidArgumentException::class);
    $this->_pageController->setDataMapper(0);
});

test('classe data mapper nao informada e metodo nao subclassificado lanca excecao', function () {
    $this->expectException(Core_Controller_Page_Exception::class);
    $this->_pageController->getDataMapper();
});

test('atribuicao de instancia entity', function () {
    $this->_pageController->setEntity(new CoreExt_EntityStub);
    $this->assertInstanceOf('CoreExt_Entity', $this->_pageController->getEntity());
});

test('instancia um entity caso nenhuma instancia tenha sido atribuida explicitamente', function () {
    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertInstanceOf('CoreExt_Entity', $this->_pageController->getEntity());
});

test('numero do processo configurado', function () {
    $this->_pageController->_processoAp = 1;
    $this->assertIsInt($this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
    $this->assertEquals(1, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(['processoAp' => 2]);
    $this->assertEquals(2, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setBaseProcessoAp(3);
    $this->assertEquals(3, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setBaseProcessoAp()".');
});

test('numero do processo nao informado e metodo nao subclassificado lanca excecao', function () {
    $this->expectException(Core_Controller_Page_Exception::class);
    $this->_pageController->getBaseProcessoAp();
});

test('titulo configurado', function () {
    $this->_pageController->_titulo = 'foo';
    $this->assertIsString($this->_pageController->getBaseTitulo(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
    $this->assertEquals('foo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(['titulo' => 'bar']);
    $this->assertEquals('bar', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setBaseTitulo('zoo');
    $this->assertEquals('zoo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setBaseTitulo()".');
});

test('titulo nao informado e metodo nao subclassificado lanca excecao', function () {
    $this->expectException(Core_Controller_Page_Exception::class);
    $this->_pageController->getBaseTitulo();
});

test('append output', function () {
    $this->_pageController->appendOutput('string 1')
        ->appendOutput('string 2');

    $this->assertEquals(
        'string 1' . PHP_EOL . 'string 2',
        $this->_pageController->getAppendedOutput(),
        '->getAppendedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
    );
});

test('get apended output retorna null quando nao existe conteudo a ser adicionado', function () {
    $this->assertNull($this->_pageController->getAppendedOutput());
});

test('prepend output', function () {
    $this->_pageController->prependOutput('string 1')
        ->prependOutput('string 2');

    $this->assertEquals(
        'string 1' . PHP_EOL . 'string 2',
        $this->_pageController->getPrependedOutput(),
        '->getPrependedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
    );
});

test('get prepended output retorna null quando nao existe conteudo a ser adicionado', function () {
    $this->assertNull($this->_pageController->getPrependedOutput());
});
