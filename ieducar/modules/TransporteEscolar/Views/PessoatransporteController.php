<?php

use iEducar\Support\View\SelectOptions;

class PessoatransporteController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
    protected $_titulo = 'Usu&aacute;rios de transporte';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_processoAp = 21240;
    protected $_deleteOption = true;

    protected $_formMap = [

        'id' => [
            'label' => 'Código',
            'help' => '',
        ],
        'pessoa' => [
            'label' => 'Pessoa',
            'help' => '',
        ],
        'rota' => [
            'label' => 'Rota',
            'help' => '',
        ],
        'ponto' => [
            'label' => 'Ponto de embarque',
            'help' => '',
        ],
        'destino' => [
            'label' => 'Destino (Caso for diferente da rota)',
            'help' => '',
        ],
        'observacao' => [
            'label' => 'Observações',
            'help' => '',
        ],
        'turno' => [
            'label' => 'Turno',
            'help' => '',
        ],
    ];

    protected function _preConstruct()
    {
        $this->_options = $this->mergeOptions([
            'edit_success' => '/intranet/transporte_pessoa_lst.php',
            'delete_success' => '/intranet/transporte_pessoa_lst.php'
        ], $this->_options);
        $nomeMenu = $this->getRequest()->id == null ? 'Cadastrar' : 'Editar';
        $this->breadcrumb("$nomeMenu usu&aacute;rio de transporte", [
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
        $this->url_cancelar = '/intranet/transporte_pessoa_lst.php';

        // Código do vinculo
        $options = [
            'label' => $this->_getLabel('id'),
            'disabled' => true,
            'required' => false,
            'size' => 25
        ];
        $this->inputsHelper()->integer('id', $options);

        // Pessoa
        $options = ['label' => $this->_getLabel('pessoa'), 'required' => true];
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        // Montar o inputsHelper->select \/
        // Cria lista de rotas
        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderBy(' descricao asc ');
        $lista_rota = $obj_rota->lista();
        $rota_resources = ['' => 'Selecione uma rota'];
        foreach ($lista_rota as $reg) {
            $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']} - {$reg['ano']}";
        }

        // Rota
        $options = [
            'label' => $this->_getLabel('rota'),
            'required' => true,
            'resources' => $rota_resources
        ];
        $this->inputsHelper()->select('rota', $options);

        // Ponto de Embarque
        $options = [
            'label' => $this->_getLabel('ponto'),
            'required' => false,
            'resources' => ['' => 'Selecione uma rota acima']
        ];
        $this->inputsHelper()->select('ponto', $options);

        // Destino
        $options = ['label' => $this->_getLabel('destino'), 'required' => false];
        $this->inputsHelper()->simpleSearchPessoaj('destino', $options);

        // observacoes
        $options = [
            'label' => $this->_getLabel('observacao'),
            'required' => false,
            'size' => 50,
            'max_length' => 255
        ];
        $this->inputsHelper()->textArea('observacao', $options);

        $this->inputsHelper()->select('turno', [
            'required' => false,
            'resources' => SelectOptions::transportPeriods(),
        ]);

        $this->loadResourceAssets($this->getDispatcher());
    }
}
