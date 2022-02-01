<?php

class IndexController extends Core_Controller_Page_ListController
{
    protected $_dataMapper = 'AreaConhecimento_Model_AreaDataMapper';

    protected $_titulo = 'Listagem de áreas de conhecimento';

    protected $_processoAp = 945;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Seção' => 'secao',
        'Agrupa descritores' => 'agrupar_descritores'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Listagem de áreas de conhecimento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function getEntries()
    {
        $areas = $this->getDataMapper()->findAll();

        foreach ($areas as $key => $area) {
            $descriptorsGroup = $area->agrupar_descritores ? 'Sim' : 'Não';
            $areas[$key]->agrupar_descritores = $descriptorsGroup;
        }

        return $areas;
    }
}
