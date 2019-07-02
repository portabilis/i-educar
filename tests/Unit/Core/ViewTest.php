<?php

use Tests\TestCase;

require_once __DIR__.'/_stub/View.php';
require_once __DIR__.'/Controller/_stub/Page/Abstract.php';

class Core_ViewTest extends TestCase
{
  protected $_pageController = NULL;
  protected $_view = NULL;

  public function __construct($name = null, array $data = [], $dataName = '')
  {
      parent::__construct($name, $data, $dataName);
  }

  protected function setUp(): void
  {
      parent::setUp();

      $this->_pageController = new Core_Controller_Page_AbstractStub();
      $this->_pageController->setOptions(array('processoAp' => 1, 'titulo' => 'foo'));
      $this->_view = new Core_ViewStub($this->_pageController);
  }

  public function testTituloConfiguradoComValorDeConfiguracaoGlobal()
  {
    $instituicao = config('legacy.app.template.vars.instituicao');

    $this->_view->MakeAll();
    $this->assertEquals($instituicao . ' | foo', $this->_view->getTitulo());
  }

  public function testProcessoApConfiguradoPeloValorDePageController()
  {
    $this->_view->MakeAll();
    $this->assertEquals(1, $this->_view->getProcessoAp());
  }
}
