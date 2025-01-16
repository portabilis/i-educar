<?php

use Tests\TestCase;

class Core_ViewTest extends TestCase
{
    protected $_pageController = null;

    protected $_view = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->_pageController = new Core_Controller_Page_AbstractStub;
        $this->_pageController->setOptions(['processoAp' => 1, 'titulo' => 'foo']);
        $this->_view = new Core_ViewStub($this->_pageController);
    }

    public function test_titulo_configurado_com_valor_de_configuracao_global()
    {
        $instituicao = config('legacy.app.template.vars.instituicao');

        $this->_view->MakeAll();
        $this->assertEquals($instituicao . ' | foo', $this->_view->getTitulo());
    }

    public function test_processo_ap_configurado_pelo_valor_de_page_controller()
    {
        $this->_view->MakeAll();
        $this->assertEquals(1, $this->_view->getProcessoAp());
    }
}
