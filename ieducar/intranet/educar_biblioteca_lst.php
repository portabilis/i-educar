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

    public $cod_biblioteca;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $nm_biblioteca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Biblioteca - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Biblioteca',
            'Escola'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        // Filtros de Foreign Keys
        $get_escola = true;
        include('include/pmieducar/educar_campo_lista.php');

        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_biblioteca', 'Biblioteca', $this->nm_biblioteca, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_biblioteca = new clsPmieducarBiblioteca();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_biblioteca->codUsuario = $this->pessoa_logada;
        }

        $obj_biblioteca->setOrderby('nm_biblioteca ASC');
        $obj_biblioteca->setLimite($this->limite, $this->offset);

        $lista = $obj_biblioteca->lista(
            null,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->nm_biblioteca,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_biblioteca->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $idpes = $det_ref_cod_escola['ref_idpes'];

                $obj_escola = new clsPessoaJuridica($idpes);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];

                $lista_busca = [
                    "<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['nm_biblioteca']}</a>",
                    "<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['ref_cod_escola']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_biblioteca_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(591, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_biblioteca_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de bibliotecas', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Biblioteca';
        $this->processoAp = '591';
    }
};
