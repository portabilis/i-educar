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

        Portabilis_View_Helper_Application::loadStylesheet(
            $this,
            'intranet/styles/localizacaoSistema.css'
        );

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Detalhe da tabela de arredondamento'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
