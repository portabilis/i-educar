<?php

require_once 'Core/Controller/Page/ViewController.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';

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

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Detalhe da &aacute;rea de conhecimento'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }

    public function getEntry()
    {
        $area = $this->getDataMapper()->find($this->getRequest()->id);
        $area->agrupar_descritores = $area->agrupar_descritores ? 'Sim' : 'Não';

        return $area;
    }
}
