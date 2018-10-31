<?php

require_once 'Core/Controller/Page/ViewController.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

class ViewController extends Core_Controller_Page_ViewController
{
    protected $_dataMapper = 'Docente_Model_LicenciaturaDataMapper';
    protected $_titulo     = 'Detalhes da licenciatura';
    protected $_processoAp = 635;
    protected $_tableMap   = [
        'Licenciatura'     => 'licenciatura',
        'Curso'            => 'curso',
        'Ano de conclusão' => 'anoConclusao',
        'IES'              => 'ies'
    ];

    public function setUrlEditar(CoreExt_Entity $entry)
    {
        $this->url_editar = CoreExt_View_Helper_UrlHelper::url(
            'edit',
            [
                'query' => [
                    'id'          => $entry->id,
                    'servidor'    => $entry->servidor,
                    'instituicao' => $this->getRequest()->instituicao
                ]
            ]
        );
    }

    public function setUrlCancelar(CoreExt_Entity $entry)
    {
        $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url(
            'index',
            [
                'query' => [
                    'id'          => $entry->id,
                    'servidor'    => $entry->servidor,
                    'instituicao' => $this->getRequest()->instituicao
                ]
            ]
        );
    }
}
