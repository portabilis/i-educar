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

    public $ref_cod_aluno;
    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Hist&oacute;rico Escolar - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = $val;
        }

        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect('educar_aluno_lst.php');
        }

        $lista_busca = [
            'Ano',
            'Extra-curricular'
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        $lista_busca[] = 'Escola';
        $lista_busca[] = 'Institui&ccedil;&atilde;o';
        $lista_busca = array_merge($lista_busca, ['Curso', 'Série', 'Registro', 'Livro', 'Folha']);

        $this->addCabecalhos($lista_busca);

        $get_escola = true;

        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, false);

        $opcoes = [ '' => 'Selecione', 2 => 'N&atilde;o', 1 => 'Sim' ];
        $this->campoLista('extra_curricular', 'Extra-curricular', $opcoes, $this->extra_curricular, '', false, '', '', false, false);

        if ($this->extra_curricular == 2) {
            $this->extra_curricular = 0;
        }

//      if ($this->ref_cod_escola)
//      {
//          $obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
//          $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
//          $this->escola = $det_ref_cod_escola["nome"];
//      }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_historico_escolar = new clsPmieducarHistoricoEscolar();
        $obj_historico_escolar->setOrderby('ano, sequencial ASC');
        $obj_historico_escolar->setLimite($this->limite, $this->offset);

        $lista = $obj_historico_escolar->lista(
            $this->ref_cod_aluno,
            null,
            null,
            null,
            null,
            $this->ano,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            $this->ref_cod_instituicao,
            null,
            $this->extra_curricular,
            null
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
                    $registro['extra_curricular'] = 'N&atilde;o';
                }

                $lista_busca = [
                    "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['ano']}</a>",

                    "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}\">{$registro['extra_curricular']}</a>"
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
        $this->addPaginador2('educar_historico_escolar_lst.php', $total, $_GET, $this->nome, $this->limite);
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

        $permissaoCadastra = $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7);
        $historicoRestringido = Portabilis_Date_Utils::brToPgSQL($historico_restringido);
        $nivelUsuarioSuperior = ($this->nivel_usuario == 1 || $this->nivel_usuario == 2);

        if ($permissaoCadastra && ($nivelUsuarioSuperior || !$historicoRestringido || $usuarioEscolaAluno)) {
            $this->acao = "go(\"educar_historico_escolar_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

        $this->largura = '100%';

        $this->breadcrumb('Atualização de históricos escolares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribui&ccedil;&atilde;o de uniforme';
        $this->processoAp = '578';
    }
};
