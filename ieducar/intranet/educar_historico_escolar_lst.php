<?php

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $ref_cod_aluno;

    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Histórico Escolar - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = $val;
        }

        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect('educar_aluno_lst.php');
        }

        $lista_busca = [
            'Ano',
            'Extra-curricular',
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        $lista_busca[] = 'Escola';
        $lista_busca[] = 'Instituição';
        $lista_busca = array_merge($lista_busca, ['Curso', 'Série', 'Registro', 'Livro', 'Folha']);

        $this->addCabecalhos($lista_busca);

        $get_escola = true;

        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4);

        $opcoes = ['' => 'Selecione', 2 => 'Não', 1 => 'Sim'];
        $this->campoLista(nome: 'extra_curricular', campo: 'Extra-curricular', valor: $opcoes, default: $this->extra_curricular, obrigatorio: false);

        if ($this->extra_curricular == 2) {
            $this->extra_curricular = 0;
        }

        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_historico_escolar = new clsPmieducarHistoricoEscolar();
        $obj_historico_escolar->setOrderby('ano, sequencial ASC');
        $obj_historico_escolar->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_historico_escolar->lista(
            int_ref_cod_aluno: $this->ref_cod_aluno,
            int_ano: $this->ano,
            int_ativo: 1,
            int_ref_cod_instituicao: $this->ref_cod_instituicao,
            int_extra_curricular: $this->extra_curricular
        );

        $total = $obj_historico_escolar->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                if ($registro['extra_curricular']) {
                    $registro['extra_curricular'] = 'Sim';
                } else {
                    $registro['extra_curricular'] = 'Não';
                }

                $lista_busca = [
                    "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['ano']}</a>",

                    "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['extra_curricular']}</a>",
                ];

                $lista_busca[] = "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['escola']}</a>";

                $lista_busca[] = "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['ref_cod_instituicao']}</a>";

                $lista_busca[] = $registro['nm_curso'];
                $lista_busca[] = $registro['nm_serie'];
                $lista_busca[] = $registro['registro'];
                $lista_busca[] = $registro['livro'];
                $lista_busca[] = $registro['folha'];

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_historico_escolar_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        $this->obj_permissao = new clsPermissoes();
        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

        $db = new clsBanco();
        $escolaAluno = $db->CampoUnico("SELECT ref_ref_cod_escola
                                          FROM pmieducar.matricula
                                         WHERE ref_cod_aluno = {$this->ref_cod_aluno}
                                           AND ativo = 1
                                         ORDER BY data_cadastro DESC
                                         LIMIT 1;");

        if ($escolaAluno) {
            $historicoRestringido = $db->CampoUnico("SELECT restringir_historico_escolar
                                                       FROM pmieducar.instituicao
                                                      WHERE cod_instituicao = (SELECT ref_cod_instituicao
                                                                                 FROM pmieducar.escola
                                                                                WHERE cod_escola = $escolaAluno);");
        }

        $escolasUsuario = new clsPmieducarEscolaUsuario();
        $escolasUsuario = $escolasUsuario->lista($this->pessoa_logada);
        $usuarioEscolaAluno = false;

        foreach ($escolasUsuario as $escolaUsuario) {
            $usuarioEscolaAluno = $escolaAluno == $escolaUsuario['ref_cod_escola'];

            if ($usuarioEscolaAluno) {
                break;
            }
        }

        $permissaoCadastra = $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
        $historicoRestringido = Portabilis_Date_Utils::brToPgSQL($historico_restringido);
        $nivelUsuarioSuperior = ($this->nivel_usuario == 1 || $this->nivel_usuario == 2);

        if ($permissaoCadastra && ($nivelUsuarioSuperior || !$historicoRestringido || $usuarioEscolaAluno)) {
            $this->acao = "go(\"educar_historico_escolar_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Atualização de históricos escolares', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Atualização de histórico escolar';
        $this->processoAp = '578';
    }
};
