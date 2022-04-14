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

    public $id;
    public $descricao;
    public $observacoes;

    public function Gerar()
    {
        $this->titulo = 'Categoria de obras - Listagem';

        //passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        // outros Filtros
        $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 49, 255, false);

        $this->addCabecalhos(['Descri&ccedil;&atilde;o', 'Observa&ccedil;&otilde;es']);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_categoria_obra = new clsPmieducarCategoriaObra();
        $obj_categoria_obra->setOrderby('descricao ASC');
        $obj_categoria_obra->setLimite($this->limite, $this->offset);

        $lista = $obj_categoria_obra->lista($this->descricao);

        $total = $obj_categoria_obra->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_categoria_obra_det.php?id={$registro['id']}\">{$registro['descricao']}</a>",
                    "<a href=\"educar_categoria_obra_det.php?id={$registro['id']}\">{$registro['observacoes']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_categoria_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_categoria_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de categorias', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Categoria de obras';
        $this->processoAp = 599;
    }
};
