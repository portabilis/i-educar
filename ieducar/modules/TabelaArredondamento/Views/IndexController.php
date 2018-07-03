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

        Portabilis_View_Helper_Application::loadStylesheet(
            $this,
            'intranet/styles/localizacaoSistema.css'
        );

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Listagem de tabelas de arredondamento'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
