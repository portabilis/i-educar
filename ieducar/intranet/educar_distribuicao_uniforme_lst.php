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

    public $cod_distribuicao_uniforme;
    public $ref_cod_aluno;
    public $ano;
    public $kit_completo;

    public function Gerar()
    {
        $this->titulo = 'Distribui&ccedil;&atilde;o de uniforme - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = $val;
        }

        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect('educar_aluno_lst.php');
        }

        $this->addCabecalhos([ 'Ano', 'Kit completo', 'Data da distribu&ccedil;&atilde;o']);

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];
        }

        if ($nm_aluno) {
            $this->campoRotulo('nm_aluno', 'Aluno', "{$nm_aluno}");
        }

        $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsPmieducarDistribuicaoUniforme();
        $obj->setOrderby('ano ASC');
        $obj->setLimite($this->limite, $this->offset);

        $lista = $obj->lista(
            $this->ref_cod_aluno,
            $this->ano
        );

        $total = $obj->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['kit_completo'] = dbBool($registro['kit_completo']) ? 'Sim' : 'Não';
                $data = Portabilis_Date_Utils::pgSQLToBr($registro['data']);
                $lista_busca = [
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&cod_distribuicao_uniforme={$registro['cod_distribuicao_uniforme']}\">{$registro['ano']}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&cod_distribuicao_uniforme={$registro['cod_distribuicao_uniforme']}\">{$registro['kit_completo']}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&cod_distribuicao_uniforme={$registro['cod_distribuicao_uniforme']}\">{$data}</a>"
                ];

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_distribuicao_uniforme_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->acao = "go(\"educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

        $this->largura = '100%';

        $this->breadcrumb('Distribuições de uniforme escolar', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribui&ccedil;&atilde;o de uniforme';
        $this->processoAp = '578';
    }
};
