<?php

class EmprestimoController extends Portabilis_Controller_Page_ListController
{
    protected $_dataMapper = '';
    protected $_titulo     = 'Emprestimo';
    protected $_formMap    = [];
    protected $_processoAp = 610;

    protected function _preRender()
    {
        $pessoa_logada = $this->pessoa_logada;

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(610, $pessoa_logada, 7, '/intranet/educar_biblioteca_index.php');

        parent::_preRender();

        $this->breadcrumb('Empréstimo de exemplares', [
          url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
      ]);
    }

    public function Gerar()
    {
        // inputs
        $this->inputsHelper()->dynamic('instituicao', ['id' => 'instituicao_id']);
        $this->inputsHelper()->dynamic('escola', ['id' => 'escola_id']);
        $this->inputsHelper()->dynamic('biblioteca', ['id' => 'biblioteca_id']);
        $this->campoNumero('tombo_exemplar', 'Tombo exemplar', '', 13, 13, true);

        $helperOptions = ['hiddenInputOptions' => ['id' => 'cliente_id']];
        $this->inputsHelper()->dynamic('bibliotecaPesquisaCliente', [], $helperOptions);

        // assets
        $this->loadResourceAssets($this->getDispatcher());
    }
}
