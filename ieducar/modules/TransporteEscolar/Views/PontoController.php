<?php

use iEducar\Modules\Addressing\LegacyAddressingFields;

class PontoController extends Portabilis_Controller_Page_EditController
{
    use LegacyAddressingFields;

    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'Pontos';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 21239;
    protected $_deleteOption = true;

    protected $_formMap = [

        'id' => [
            'label' => 'Código do ponto',
            'help' => '',
        ],
        'desc' => [
            'label' => 'Descrição',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_ponto_lst.php',
            'delete_success' => '/intranet/transporte_ponto_lst.php'
        ], $this->_options);
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $this->breadcrumb("$nomeMenu ponto", [
            url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
        ]);
    }

    protected function _initNovo()
    {
        return false;
    }

    protected function _initEditar()
    {
        return false;
    }

    public function Gerar()
    {
        $this->url_cancelar = '/intranet/transporte_ponto_lst.php';

        // Código do ponto
        $options = [
            'label' => $this->_getLabel('id'),
            'disabled' => true,
            'required' => false,
            'size' => 25
        ];
        $this->inputsHelper()->integer('id', $options);

        // descricao
        $options = [
            'label' => $this->_getLabel('desc'),
            'required' => true,
            'size' => 50,
            'max_length' => 70
        ];
        $this->inputsHelper()->text('desc', $options);

        $this->viewAddress();

        $this->inputsHelper()->text('latitude', ['required' => false]);

        $this->inputsHelper()->text('longitude', ['required' => false]);

        $script = [
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
            '/lib/Utils/gmaps.js',
            '/modules/Portabilis/Assets/Javascripts/Frontend/ieducar.singleton_gmap.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $script);

        $this->loadResourceAssets($this->getDispatcher());
    }
}
