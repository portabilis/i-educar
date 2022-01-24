<?php

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'FormulaMedia_Model_FormulaDataMapper';
    protected $_titulo     = 'Listagem de fórmulas de cálculo de média';
    protected $_processoAp = 948;
    protected $_tableMap   = [
    'Nome' => 'nome',
    'Fórmula de cálculo' => 'formulaMedia',
    'Tipo fórmula' => 'tipoFormula'
  ];

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de fórmulas de média', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }
}
