<?php

class ViewController extends Core_Controller_Page_ViewController
{
    protected $_dataMapper = 'ComponenteCurricular_Model_ComponenteDataMapper';

    protected $_titulo = 'Detalhes de área de conhecimento';

    protected $_processoAp = 946;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Abreviatura' => 'abreviatura',
        'Base curricular' => 'tipo_base',
        'Área conhecimento' => 'area_conhecimento'
    ];

    /**
     * Construtor.
     */
    public function __construct()
    {
        // Apenas faz o override do construtor
    }

    protected function _preRender()
    {
        parent::_preRender();

        $this->breadcrumb('Detalhe do componente curricular', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function setUrlCancelar(CoreExt_Entity $entry)
    {
        $this->url_cancelar = 'intranet/educar_componente_curricular_lst.php';
    }
}
