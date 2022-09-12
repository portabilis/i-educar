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

    public $cod_disciplina_topico;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_topico;
    public $desc_topico;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Disciplina Tópico - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Nome Tópico'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto('nm_topico', 'Nome Tópico', $this->nm_topico, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_disciplina_topico = new clsPmieducarDisciplinaTopico();
        $obj_disciplina_topico->setOrderby('nm_topico ASC');
        $obj_disciplina_topico->setLimite($this->limite, $this->offset);

        $lista = $obj_disciplina_topico->lista(
            null,
            null,
            null,
            $this->nm_topico,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_disciplina_topico->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_disciplina_topico_det.php?cod_disciplina_topico={$registro['cod_disciplina_topico']}\">{$registro['nm_topico']}</a>",
                ]);
            }
        }
        $this->addPaginador2('educar_disciplina_topico_lst.php', $total, $_GET, $this->nome, $this->limite);

        $objPermissao = new clsPermissoes();
        if ($objPermissao->permissao_cadastra(565, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_disciplina_topico_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Disciplina Tópico';
        $this->processoAp = '565';
    }
};
