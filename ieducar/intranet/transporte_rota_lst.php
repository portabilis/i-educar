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

    public $descricao;
    public $ref_idpes_destino;
    public $ano;
    public $tipo_rota;
    public $km_pav;
    public $km_npav;
    public $ref_cod_empresa_transporte_escolar;
    public $tercerizado;
    public $nome_destino;

    public function Gerar()
    {
        $this->titulo = 'Rotas - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Ano',
            'Código da rota',
            'Descrição',
            'Destino',
            'Empresa',
            'Terceirizado'
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

        $this->campoLista('ref_cod_empresa_transporte_escolar', 'Empresa', $opcoes, $this->ref_cod_empresa_transporte_escolar, '', false, '', '', false, false);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 50, 30);
        $this->campoNumero('ano', 'Ano', $this->cnh, 4, 5);
        $this->campoTexto('nome_destino', 'Destino', $this->nome_destino, 50, 30);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby(' descricao ASC');
        $obj_rota->setLimite($this->limite, $this->offset);

        $lista = $obj_rota->lista(
            null,
            $this->descricao,
            null,
            $this->nome_destino,
            $this->ano,
            $this->ref_cod_empresa_transporte_escolar
        );

        $total = $obj_rota->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">{$registro['ano']}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">{$registro['cod_rota_transporte_escolar']}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">{$registro['descricao']}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">{$registro['nome_destino']}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">{$registro['nome_empresa']}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro['cod_rota_transporte_escolar']}\">".($registro['tercerizado'] == 'S'? 'Sim' : 'Não').'</a>'
                ]);
            }
        }

        $this->addPaginador2('transporte_rota_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21238, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("/module/TransporteEscolar/Rota")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de rotas', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Rotas';
        $this->processoAp = '21238';
    }
};
