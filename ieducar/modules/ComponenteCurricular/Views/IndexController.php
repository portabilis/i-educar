<?php

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

        $this->breadcrumb('Listagem de componentes curriculares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}
