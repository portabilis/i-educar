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

    public $cod_projeto;
    public $nome;
    public $observacao;

    public function Gerar()
    {
        $this->titulo = 'Projetos - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Nome do projeto',
            'Observação'
        ]);

        $this->campoTexto('nome', 'Nome do projeto', $this->nome, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_projeto = new clsPmieducarProjeto();
        $obj_projeto->setOrderby('nome ASC');
        $obj_projeto->setLimite($this->limite, $this->offset);

        $lista = $obj_projeto->lista(
            null,
            $this->nome
        );

        $total = $obj_projeto->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}\">{$registro['observacao']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_projeto_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21250, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_projeto_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $this->breadcrumb('Listagem de projetos', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
