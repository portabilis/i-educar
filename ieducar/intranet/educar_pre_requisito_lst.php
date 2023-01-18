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

    public $cod_pre_requisito;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $schema_;
    public $tabela;
    public $nome;
    public $sql;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Pre Requisito - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos(coluna: [
            'Nome',
            'Schema ',
            'Tabela',
            'Sql'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto(nome: 'nome', campo: 'Nome', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'schema_', campo: 'Schema ', valor: $this->schema_, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'tabela', campo: 'Tabela', valor: $this->tabela, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_pre_requisito = new clsPmieducarPreRequisito();
        $obj_pre_requisito->setOrderby(strNomeCampo: 'nome ASC');
        $obj_pre_requisito->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_pre_requisito->lista(
            int_cod_pre_requisito: $this->cod_pre_requisito,
            str_schema_: $this->schema_,
            str_tabela: $this->tabela,
            str_nome: $this->nome,
            str_sql: $this->sql,
            date_data_exclusao_ini: 1
        );

        $total = $obj_pre_requisito->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(datetime: substr(string: $registro['data_cadastro'], offset: 0, length: 16));
                $registro['data_cadastro_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(datetime: substr(string: $registro['data_exclusao'], offset: 0, length: 16));
                $registro['data_exclusao_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_exclusao_time']);

                $obj_ref_usuario_exc = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $obj_ref_usuario_cad = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_cad']);
                $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
                $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

                $this->addLinhas(linha: [
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro['cod_pre_requisito']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro['cod_pre_requisito']}\">{$registro['schema_']}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro['cod_pre_requisito']}\">{$registro['tabela']}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro['cod_pre_requisito']}\">{$registro['sql']}</a>"
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_pre_requisito_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, super_usuario: true)) {
            $this->acao = 'go("educar_pre_requisito_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pre Requisito';
        $this->processoAp = '601';
    }
};
