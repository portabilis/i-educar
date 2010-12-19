<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Core_Controller
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/_stub/Page/Abstract.php';
require_once 'CoreExt/_stub/EntityDataMapper.php';

/**
 * Core_Controller_Page_AbstractTest class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_AbstractTest extends UnitBaseTest
{
  protected $_pageController = NULL;

  protected function setUp()
  {
    $this->_pageController = new Core_Controller_Page_AbstractStub();
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_pageController->setOptions(array('foo' => 'bar'));
  }

  public function testClasseDataMapperEGeradaAPartirDaDefinicaoString()
  {
    $this->_pageController->_dataMapper = 'CoreExt_EntityDataMapperStub';
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('datamapper' => 'CoreExt_EntityDataMapperStub'));
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com nome de classe "CoreExt_DataMapper".');

    $this->_pageController->setDataMapper(new CoreExt_EntityDataMapperStub());
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asserção a partir de configuração via "setDataMapper()" com objeto "CoreExt_DataMapper".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testClasseDataMapperNaoExistenteLancaExcecao()
  {
    $this->_pageController->setDataMapper('FooDataMapper');
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testMetodoLancaExcecaoQuandoNaoRecebeTipoSuportado()
  {
    $this->_pageController->setDataMapper(0);
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testClasseDataMapperNaoInformadaEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getDataMapper();
  }

  public function testAtribuicaoDeInstanciaEntity()
  {
    $this->_pageController->setEntity(new CoreExt_EntityStub());
    $this->assertType('CoreExt_Entity', $this->_pageController->getEntity());
  }

  /**
   * Ao usar o typehinting do PHP, é verificado se o parâmetro é do tipo
   * correto. Se não for, um fatal error é lançado. O PHPUnit converte esse
   * erro em Exception para tornar o teste mais fácil.
   *
   * @expectedException PHPUnit_Framework_Error
   */
  public function testAtribuicaoDeInstanciaEntityLancaExcecaoParaTipoNaoSuportado()
  {
    $this->_pageController->setEntity(NULL);
  }

  public function testInstanciaUmEntityCasoNenhumaInstanciaTenhaSidoAtribuidaExplicitamente()
  {
    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertType('CoreExt_Entity', $this->_pageController->getEntity());
  }

  public function testNumeroDoProcessoConfigurado()
  {
    $this->_pageController->_processoAp = 1;
    $this->assertType('int', $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
    $this->assertEquals(1, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('processoAp' => 2));
    $this->assertEquals(2, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setBaseProcessoAp(3);
    $this->assertEquals(3, $this->_pageController->getBaseProcessoAp(), 'Falhou na asserção a partir de configuração via "setBaseProcessoAp()".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testNumeroDoProcessoNaoInformadoEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getBaseProcessoAp();
  }

  public function testTituloConfigurado()
  {
    $this->_pageController->_titulo = 'foo';
    $this->assertType('string', $this->_pageController->getBaseTitulo(), 'Falhou na asserção por tipo a partir da instanciação de "Core_Page_Controller_Abstract".');
    $this->assertEquals('foo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção por valor a partir da instanciação de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('titulo' => 'bar'));
    $this->assertEquals('bar', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setOptions()".');

    $this->_pageController->setBaseTitulo('zoo');
    $this->assertEquals('zoo', $this->_pageController->getBaseTitulo(), 'Falhou na asserção a partir de configuração via "setBaseTitulo()".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testTituloNaoInformadoEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getBaseTitulo();
  }

  public function testAppendOutput()
  {
    $this->_pageController->appendOutput('string 1')
                          ->appendOutput('string 2');

    $this->assertEquals(
      'string 1' . PHP_EOL . 'string 2',
      $this->_pageController->getAppendedOutput(),
      '->getAppendedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
    );
  }

  public function testGetApendedOutputRetornaNullQuandoNaoExisteConteudoASerAdicionado()
  {
    $this->assertNull($this->_pageController->getAppendedOutput());
  }

  public function testPrependOutput()
  {
    $this->_pageController->prependOutput('string 1')
                          ->prependOutput('string 2');

    $this->assertEquals(
      'string 1' . PHP_EOL . 'string 2',
      $this->_pageController->getPrependedOutput(),
      '->getPrependedOutput() retorna o conteúdo a ser adicionado como uma string separada por quebra de linha'
    );
  }

  public function testGetPrependedOutputRetornaNullQuandoNaoExisteConteudoASerAdicionado()
  {
    $this->assertNull($this->_pageController->getPrependedOutput());
  }
}
