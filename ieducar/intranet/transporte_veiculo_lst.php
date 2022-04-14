<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_veiculo;
    public $descricao;
    public $placa;
    public $renavam;
    public $marca;
    public $ativo;
    public $cod_empresa;
    public $nome_motorista;

    public function Gerar()
    {
        $this->titulo = 'Veículos - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Código do veículo',
            'Descrição',
            'Placa',
            'Marca',
            'Empresa',
            'Motorista responsável'
        ]);

        // Filtros de Foreign Keys
        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsModulesEmpresaTransporteEscolar();
        $objTemp->setOrderby(' nome_empresa ASC');
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_empresa_transporte_escolar']}"] = "{$registro['nome_empresa']}";
            }
        } else {
            $opcoes = [ '' => 'Sem empresas cadastradas' ];
        }

        $this->campoNumero('cod_veiculo', 'Código do veículo', $this->cod_veiculo, 29, 15);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 29, 50, false);
        $this->campoTexto('placa', 'Placa', $this->placa, 29, 10, false);
        $this->campoTexto('renavam', 'Renavam', $this->renavam, 29, 30, false);
        $this->campoTexto('marca', 'Marca', $this->marca, 29, 50, false);

        $this->campoLista('ativo', 'Ativo', [ null => 'Selecione', 'S' => 'Ativo', 'N' => 'Inativo'], $this->ativo, '', false, '', '', false, false);
        $this->campoLista('cod_empresa', 'Empresa', $opcoes, $this->cod_empresa, '', false, '', '', false, false);
        $this->campoTexto('nome_motorista', 'Motorista responsável', $this->nome_motorista, 29, 30, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsModulesVeiculo();
        $obj->setOrderby(' descricao ASC');
        $obj->setLimite($this->limite, $this->offset);

        $lista = $obj->lista(
            $this->cod_veiculo,
            $this->descricao,
            $this->placa,
            $this->renavam,
            $this->nome_motorista,
            $this->cod_empresa,
            $this->marca,
            $this->ativo
        );

        $total = $obj->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['cod_veiculo']}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['descricao']}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['placa']}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['marca']}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['nome_empresa']}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro['cod_veiculo']}\">{$registro['nome_motorista']}</a>"
                ]);
            }
        }

        $this->addPaginador2('transporte_veiculo_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21237, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("/module/TransporteEscolar/Veiculo")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de ve&iacute;culos', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Veículos';
        $this->processoAp = '21237';
    }
};
