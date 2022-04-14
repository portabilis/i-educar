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

    public $cod_deficiencia;
    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Defici&ecirc;ncia - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Defici&ecirc;ncia'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto('nm_deficiencia', 'Deficiência', $this->nm_deficiencia, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_deficiencia = new clsCadastroDeficiencia();
        $obj_deficiencia->setOrderby('nm_deficiencia ASC');
        $obj_deficiencia->setLimite($this->limite, $this->offset);

        $lista = $obj_deficiencia->lista(
            $this->cod_deficiencia,
            $this->nm_deficiencia
        );

        $total = $obj_deficiencia->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data

                // pega detalhes de foreign_keys

                $this->addLinhas([
                    "<a href=\"educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}\">{$registro['nm_deficiencia']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_deficiencia_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(631, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_deficiencia_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de deficiência', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Defici&ecirc;ncia';
        $this->processoAp = '631';
    }
};
