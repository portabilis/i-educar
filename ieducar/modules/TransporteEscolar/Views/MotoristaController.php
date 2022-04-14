<?php

class MotoristaController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'Motoristas';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 21236;
    protected $_deleteOption = true;

    protected $_formMap = [
        'id' => [
            'label' => 'Código do motorista',
            'help' => '',
        ],

        'pessoa' => [
            'label' => 'Pessoa',
            'help' => '',
        ],

        'cnh' => [
            'label' => 'CNH',
            'help' => '',
        ],

        'tipo_cnh' => [
            'label' => 'Categoria CNH',
            'help' => '',
        ],

        'dt_habilitacao' => [
            'label' => 'Data da habilitação',
            'help' => '',
        ],

        'vencimento_cnh' => [
            'label' => 'Vencimento da habilitação',
            'help' => '',
        ],

        'ref_cod_empresa_transporte_escolar' => [
            'label' => 'Empresa',
            'help' => '',
        ],

        'observacao' => [
            'label' => 'Observações',
            'help' => '',
        ]

    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_motorista_lst.php',
            'delete_success' => '/intranet/transporte_motorista_lst.php'
        ], $this->_options);
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $this->breadcrumb("$nomeMenu motorista", [
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
        $this->url_cancelar = '/intranet/transporte_motorista_lst.php';

        // Código do Motorista
        $options = [
            'label' => $this->_getLabel('id'),
            'disabled' => true,
            'required' => false,
            'size' => 25
        ];
        $this->inputsHelper()->integer('id', $options);

        // nome
        $options = ['label' => $this->_getLabel('pessoa'), 'size' => 50];
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        //número da CNH
        $options = [
            'label' => $this->_getLabel('cnh'),
            'max_length' => 15,
            'size' => 15,
            'placeholder' => 'Número da CNH'
        ];
        $this->inputsHelper()->integer('cnh', $options);

        //Categoria da CNH
        $options = [
            'label' => $this->_getLabel('tipo_cnh'),
            'max_length' => 2,
            'size' => 1,
            'placeholder' => '',
            'required' => false
        ];
        $this->inputsHelper()->text('tipo_cnh', $options);

        // Vencimento
        $options = [
            'label' => $this->_getLabel('dt_habilitacao'),
            'required' => false,
            'size' => 10,
            'placeholder' => ''
        ];
        $this->inputsHelper()->date('dt_habilitacao', $options);

        // Habilitação
        $options = [
            'label' => $this->_getLabel('vencimento_cnh'),
            'required' => false,
            'size' => 10,
            'placeholder' => ''
        ];
        $this->inputsHelper()->date('vencimento_cnh', $options);

        // Codigo da empresa
        $options = [
            'label' => $this->_getLabel('ref_cod_empresa_transporte_escolar'),
            'required' => true
        ];
        $this->inputsHelper()->simpleSearchEmpresa('ref_cod_empresa_transporte_escolarf', $options);

        // observações
        $options = [
            'label' => $this->_getLabel('observacao'),
            'required' => false,
            'size' => 50,
            'max_length' => 255
        ];
        $this->inputsHelper()->textArea('observacao', $options);

        $this->loadResourceAssets($this->getDispatcher());
    }
}
