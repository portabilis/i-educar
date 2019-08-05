<?php

require_once 'Core/Controller/Page/ViewController.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';

class ViewController extends Core_Controller_Page_ViewController
{

    protected $_dataMapper = 'TabelaArredondamento_Model_TabelaDataMapper';
    protected $_titulo = 'Detalhes da tabela de arredondamento';
    protected $_processoAp = 949;
    protected $_tableMap = [
        'Nome' => 'nome',
        'Tipo nota' => 'tipoNota'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Detalhe da tabela de arredondamento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}
