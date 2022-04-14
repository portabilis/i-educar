<?php

class ReservaController extends Portabilis_Controller_Page_ListController
{
    protected $_dataMapper = ''; #Avaliacao_Model_NotaAlunoDataMapper';
    protected $_titulo     = 'Reserva';
    protected $_formMap    = [];
    protected $_processoAp = 609;

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Reserva de exemplares', [
        url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
    ]);
    }

    public function Gerar()
    {
        // inputs
        $inputs = ['instituicao', 'escola', 'biblioteca', 'bibliotecaPesquisaCliente', 'bibliotecaPesquisaObra'];
        $this->inputsHelper()->dynamic($inputs);

        // assets
        $this->loadResourceAssets($this->getDispatcher());
    }
}
