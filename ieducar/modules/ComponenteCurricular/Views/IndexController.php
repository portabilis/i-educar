<?php

require_once 'Core/Controller/Page/ListController.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'ComponenteCurricular_Model_ComponenteDataMapper';

    protected $_titulo = 'Listagem de componentes curriculares';

    protected $_processoAp = 946;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Abreviatura' => 'abreviatura',
        'Base' => 'tipo_base',
        'Área de conhecimento' => 'area_conhecimento'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Listagem de componentes curriculares'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
