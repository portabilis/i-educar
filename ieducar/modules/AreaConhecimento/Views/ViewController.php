<?php

class ViewController extends Core_Controller_Page_ViewController
{
    protected $_dataMapper = 'AreaConhecimento_Model_AreaDataMapper';

    protected $_titulo = 'Detalhes de área de conhecimento';

    protected $_processoAp = 945;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Seção' => 'secao',
        'Agrupa descritores' => 'agrupar_descritores',
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Detalhe da &aacute;rea de conhecimento', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function getEntry()
    {
        $area = $this->getDataMapper()->find($this->getRequest()->id);
        $area->agrupar_descritores = $area->agrupar_descritores ? 'Sim' : 'Não';

        return $area;
    }
}
