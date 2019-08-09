<?php

require_once 'Core/Controller/Page/ListController.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'TabelaArredondamento_Model_TabelaDataMapper';
    protected $_titulo = 'Listagem de tabelas de arredondamento de nota';
    protected $_processoAp = 949;
    protected $_tableMap = [
        'Nome' => 'nome',
        'Sistema de nota' => 'tipoNota'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de tabelas de arredondamento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}
