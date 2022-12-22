<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_distribuicao_uniforme;
    public $ref_cod_aluno;
    public $ano;
    public $kit_completo;

    public function Gerar()
    {
        $this->titulo = 'Distribuição de uniforme - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = $val;
        }

        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect('educar_aluno_lst.php');
        }

        $this->addCabecalhos([ 'Ano', 'Kit completo', 'Data da distribução']);

        $obj_permissao = new clsPermissoes();
        $obj_permissao->nivel_acesso($this->pessoa_logada);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(int_cod_aluno: $this->ref_cod_aluno, int_ref_cod_aluno_beneficio: null, int_ref_cod_religiao: null, int_ref_usuario_exc: null, int_ref_usuario_cad: null, int_ref_idpes: null, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];
        }

        if ($nm_aluno) {
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: "{$nm_aluno}");
        }

        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsPmieducarDistribuicaoUniforme();
        $obj->setOrderby('ano ASC');
        $obj->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj->lista(
            ref_cod_aluno: $this->ref_cod_aluno,
            ano: $this->ano
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
        $this->addPaginador2(strUrl: 'educar_distribuicao_uniforme_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = "go(\"educar_distribuicao_uniforme_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Distribuições de uniforme escolar', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribuição de uniforme';
        $this->processoAp = '578';
    }
};
