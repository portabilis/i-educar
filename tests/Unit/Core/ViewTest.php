<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->_pageController = new Core_Controller_Page_AbstractStub;
    $this->_pageController->setOptions(['processoAp' => 1, 'titulo' => 'foo']);
    $this->_view = new Core_ViewStub($this->_pageController);
});

test('titulo configurado com valor de configuracao global', function () {
    $instituicao = config('legacy.app.template.vars.instituicao');

    $this->_view->MakeAll();
    $this->assertEquals($instituicao . ' | foo', $this->_view->getTitulo());
});

test('processo ap configurado pelo valor de page controller', function () {
    $this->_view->MakeAll();
    $this->assertEquals(1, $this->_view->getProcessoAp());
});
