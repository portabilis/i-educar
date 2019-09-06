<?php

require_once 'Core/Controller/Page/ListController.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'AreaConhecimento_Model_AreaDataMapper';

    protected $_titulo = 'Listagem de áreas de conhecimento';

    protected $_processoAp = 945;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Seção' => 'secao'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Listagem de &aacute;reas de conhecimento'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
