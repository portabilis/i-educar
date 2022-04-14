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

    public $cod_situacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_situacao;
    public $permite_emprestimo;
    public $descricao;
    public $situacao_padrao;
    public $situacao_emprestada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public function Gerar()
    {
        $this->titulo = 'Situa&ccedil;&atilde;o - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Situa&ccedil;&atilde;o',
            'Permite Empr&eacute;stimo'
        ];

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_situacao', 'Situa&ccedil;&atilde;o', $this->nm_situacao, 30, 255, false);
        $opcoes = ['' => 'Selecione', 1 => 'n&atilde;o', 2 => 'sim' ];
        $this->campoLista('permite_emprestimo', 'Permite Empr&eacute;stimo', $opcoes, $this->permite_emprestimo, null, null, null, null, null, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_situacao = new clsPmieducarSituacao();
        $obj_situacao->setOrderby('nm_situacao ASC');
        $obj_situacao->setLimite($this->limite, $this->offset);

        $lista = $obj_situacao->lista(
            null,
            null,
            null,
            $this->nm_situacao,
            $this->permite_emprestimo,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_biblioteca,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola
        );

        $total = $obj_situacao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];
                $registro['ref_cod_instituicao'] = $det_ref_cod_biblioteca['ref_cod_instituicao'];
                $registro['ref_cod_escola'] = $det_ref_cod_biblioteca['ref_cod_escola'];
                if ($registro['ref_cod_instituicao']) {
                    $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                    $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
                }
                if ($registro['ref_cod_escola']) {
                    $obj_ref_cod_escola = new clsPmieducarEscola();
                    $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro['ref_cod_escola']));
                    $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];
                }

                if ($registro['permite_emprestimo'] == 1) {
                    $registro['permite_emprestimo'] = 'n&atilde;o';
                } elseif ($registro['permite_emprestimo'] == 2) {
                    $registro['permite_emprestimo'] = 'sim';
                }

                $lista_busca = [
                    "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['nm_situacao']}</a>",
                    "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['permite_emprestimo']}</a>"
                ];

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
                    $lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['ref_cod_biblioteca']}</a>";
                } elseif ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
                    $lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['ref_cod_biblioteca']}</a>";
                }
                if ($nivel_usuario == 1 || $nivel_usuario == 2) {
                    $lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['ref_cod_escola']}</a>";
                }
                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_situacao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(602, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_situacao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de situações', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Situa&ccedil;&atilde;o';
        $this->processoAp = '602';
    }
};
