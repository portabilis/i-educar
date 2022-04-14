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

    public $cod_infra_comodo_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $desc_funcao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public function Gerar()
    {
        $this->titulo = 'Tipo de ambiente - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Tipo de ambiente',
            'Escola',
            'Instituição'
        ];

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(['instituicao','escola'],[],['options' => ['required' => false]]);

        // outros Filtros
        $this->campoTexto('nm_funcao', 'Tipo', $this->nm_funcao, 30, 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_infra_comodo_funcao = new clsPmieducarInfraComodoFuncao();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_infra_comodo_funcao->codUsuario = $this->pessoa_logada;
        }

        $obj_infra_comodo_funcao->setOrderby('nm_funcao ASC');
        $obj_infra_comodo_funcao->setLimite($this->limite, $this->offset);

        $lista = $obj_infra_comodo_funcao->lista(
            $this->cod_infra_comodo_funcao,
            null,
            null,
            $this->nm_funcao,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao
        );

        $total = $obj_infra_comodo_funcao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro['cod_infra_comodo_funcao']}\">{$registro['nm_funcao']}</a>"
                ];

                $lista_busca[] = "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro['cod_infra_comodo_funcao']}\">{$nm_escola}</a>";
                $lista_busca[] = "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro['cod_infra_comodo_funcao']}\">{$registro['ref_cod_instituicao']}</a>";

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_infra_comodo_funcao_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(567, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_infra_comodo_funcao_cad.php")';
            $this->nome_acao = 'Novo';
            ;
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de tipos de ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo de ambiente';
        $this->processoAp = '572';
    }
};
