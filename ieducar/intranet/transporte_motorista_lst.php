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

    public $cod_motorista;
    public $nome_motorista;
    public $cod_empresa;
    public $cnh;
    public $tipo_cnh;

    public function Gerar()
    {
        $this->titulo = 'Motoristas - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Código motorista',
            'Nome',
            'CNH',
            'Categoria CNH',
            'Empresa'
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

        $this->campoLista('cod_empresa', 'Empresa', $opcoes, $this->cod_empresa, '', false, '', '', false, false);
        $this->campoNumero('cod_motorista', 'Código do motorista', $this->cod_motorista, 29, 15);
        $this->campoNumero('cnh', 'CNH', $this->cnh, 29, 15);
        $this->campoTexto('tipo_cnh', 'Categoria', $this->tipo_cnh, 2, 2, false);
        $this->campoTexto('nome_motorista', 'Nome', $this->nome_motorista, 29, 30, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_uf = new clsModulesMotorista();
        $obj_uf->setOrderby(' nome_motorista ASC');
        $obj_uf->setLimite($this->limite, $this->offset);

        $lista = $obj_uf->lista(
            $this->cod_motorista,
            $this->nome_motorista,
            $this->cnh,
            $this->tipo_cnh,
            $this->cod_empresa
        );

        $total = $obj_uf->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"transporte_motorista_det.php?cod_motorista={$registro['cod_motorista']}\">{$registro['cod_motorista']}</a>",
                    "<a href=\"transporte_motorista_det.php?cod_motorista={$registro['cod_motorista']}\">{$registro['nome_motorista']}</a>",
                    "<a href=\"transporte_motorista_det.php?cod_motorista={$registro['cod_motorista']}\">{$registro['cnh']}</a>",
                    "<a href=\"transporte_motorista_det.php?cod_motorista={$registro['cod_motorista']}\">{$registro['tipo_cnh']}</a>",
                    "<a href=\"transporte_motorista_det.php?cod_motorista={$registro['cod_motorista']}\">{$registro['nome_empresa']}</a>"
                ]);
            }
        }

        $this->addPaginador2('transporte_motorista_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21236, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("/module/TransporteEscolar/Motorista")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de motoristas', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Motoristas';
        $this->processoAp = '21236';
    }
};
