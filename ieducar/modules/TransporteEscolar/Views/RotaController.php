<?php

class RotaController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'Rotas';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 21238;
    protected $_deleteOption = true;

    protected $_formMap = [

        'id' => [
            'label' => 'Código da rota',
            'help' => '',
        ],
        'desc' => [
            'label' => 'Descrição',
            'help' => '',
        ],
        'ref_idpes_destino' => [
            'label' => 'Instituição destino',
            'help' => '',
        ],
        'ano' => [
            'label' => 'Ano',
            'help' => '',
        ],
        'tipo_rota' => [
            'label' => 'Tipo da rota',
            'help' => '',
        ],
        'km_pav' => [
            'label' => 'Km pavimentados',
            'help' => '',
        ],
        'km_npav' => [
            'label' => 'Km não pavimentados',
            'help' => '',
        ],
        'ref_cod_empresa_transporte_escolar' => [
            'label' => 'Empresa',
            'help' => '',
        ],
        'tercerizado' => [
            'label' => 'Terceirizado',
            'help' => '',
        ]
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_rota_lst.php',
            'delete_success' => '/intranet/transporte_rota_lst.php'
        ], $this->_options);
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $this->breadcrumb("$nomeMenu rota", [
            url('intranet/educar_transporte_escolar_index.php') => 'In&Transporte escolar',
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
        $this->url_cancelar = '/intranet/transporte_rota_lst.php';

        // ano
        $options = [
            'label' => $this->_getLabel('ano'),
            'required' => true,
            'size' => 5,
            'max_length' => 4
        ];
        $this->inputsHelper()->integer('ano', $options);

        // Código da rota
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
            'max_length' => 50
        ];
        $this->inputsHelper()->text('desc', $options);

        // Destino
        $options = ['label' => $this->_getLabel('ref_idpes_destino'), 'required' => true, 'size' => 50];
        $this->inputsHelper()->simpleSearchPessoaj('ref_idpes_destino', $options);

        // Empresa rota
        $options = [
            'label' => $this->_getLabel('ref_cod_empresa_transporte_escolar'),
            'required' => true,
            'size' => 50
        ];
        $this->inputsHelper()->simpleSearchEmpresa('ref_cod_empresa_transporte_escolar', $options);

        // Tipo
        $tipos = [
            null => 'Selecione um tipo',
            'U' => 'Urbana',
            'R' => 'Rural'
        ];

        $options = [
            'label' => $this->_getLabel('tipo_rota'),
            'resources' => $tipos,
            'required' => true
        ];

        $this->inputsHelper()->select('tipo_rota', $options);

        // km pavimentados
        $options = [
            'label' => $this->_getLabel('km_pav'),
            'required' => false,
            'size' => 9,
            'max_length' => 10,
            'placeholder' => ''
        ];
        $this->inputsHelper()->numeric('km_pav', $options);

        // km não pavimentados
        $options = [
            'label' => $this->_getLabel('km_npav'),
            'required' => false,
            'size' => 9,
            'max_length' => 10,
            'placeholder' => ''
        ];
        $this->inputsHelper()->numeric('km_npav', $options);

        // Tercerizado
        $options = ['label' => $this->_getLabel('tercerizado')];
        $this->inputsHelper()->checkbox('tercerizado', $options);

        $this->loadResourceAssets($this->getDispatcher());
    }
}
