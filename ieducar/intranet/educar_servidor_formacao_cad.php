<?php

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_formacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_servidor;
    public $nm_formacao;
    public $tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $passo;
    public $data_conclusao;
    public $data_registro;
    public $diplomas_registros;
    public $ref_cod_instituicao;
    public $data_vigencia_homolog;
    public $data_publicacao;
    public $cod_servidor_curso;
    public $cod_servidor_titulo;

    public function Inicializar()
    {
        $retorno = '';

        $this->cod_formacao        = $_GET['cod_formacao'];
        $this->ref_cod_servidor    = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->passo               = $_POST['passo'];
        $this->tipo                = $_POST['tipo'];

        // URL para redirecionamento
        $backUrl = sprintf(
            'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        if (is_string(value: $this->passo) && $this->passo == 1) {
            $retorno = 'Novo';
        }

        if (is_numeric(value: $this->cod_formacao)) {
            $obj = new clsPmieducarServidorFormacao(
                cod_formacao: $this->cod_formacao,
                ref_usuario_exc: null,
                ref_usuario_cad: null,
                ref_cod_servidor: $this->ref_cod_servidor,
                nm_formacao: null,
                tipo: null,
                descricao: null,
                data_cadastro: null,
                data_exclusao: null,
                ativo: 1,
                ref_ref_cod_instituicao: $this->ref_cod_instituicao
            );

            $registro  = $obj->detalhe();

            if ($registro) {
                $this->nm_formacao = $registro['nm_formacao'];
                $this->tipo        = $registro['tipo'];
                $this->descricao   = $registro['descricao'];

                if ($this->tipo == 'C') {
                    $obj_curso                = new clsPmieducarServidorCurso(cod_servidor_curso: null, ref_cod_formacao: $this->cod_formacao);
                    $det_curso                = $obj_curso->detalhe();
                    $this->data_conclusao     = dataFromPgToBr(data_original: $det_curso['data_conclusao']);
                    $this->data_registro      = dataFromPgToBr(data_original: $det_curso['data_registro']);
                    $this->diplomas_registros = $det_curso['diplomas_registros'];
                    $this->cod_servidor_curso = $det_curso['cod_servidor_curso'];
                } else {
                    $obj_outros = new clsPmieducarServidorTituloConcurso(cod_servidor_titulo: null, ref_cod_formacao: $this->cod_formacao);
                    $det_outros = $obj_outros->detalhe();
                    $this->data_vigencia_homolog = dataFromPgToBr(data_original: $det_outros['data_vigencia_homolog']);
                    $this->data_publicacao       = dataFromPgToBr(data_original: $det_outros['data_publicacao']);
                    $this->cod_servidor_titulo   = $det_outros['cod_servidor_titulo'];
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno     = 'Editar';
                $this->passo = 1;
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_servidor_formacao_det.php?cod_formacao=' . $registro['cod_formacao'] :
      $backUrl;

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if (! is_numeric(value: $this->passo)) {
            $this->passo = 1;
            $this->campoOculto(nome: 'passo', valor: $this->passo);

            $opcoes = [
        'C' => 'Cursos',
        'T' => 'Títulos',
        'O' => 'Concursos'
      ];

            $this->campoLista(nome: 'tipo', campo: 'Tipo de Formação', valor: $opcoes, default: $this->tipo);

            $this->acao_enviar = false;

            $this->array_botao[] = 'Continuar';
            $this->array_botao_url_script[] = 'acao();';

            $this->url_cancelar = false;

            $this->array_botao[] = 'Cancelar';
            $this->array_botao_url_script[] = sprintf(
                'go("educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d")',
                $this->ref_cod_servidor,
                $this->ref_cod_instituicao
            );
        } elseif (is_numeric(value: $this->passo) && $this->passo == 1) {
            if ($this->tipo == 'C') {
                // Primary keys
                $this->campoOculto(nome: 'cod_formacao', valor: $this->cod_formacao);
                $this->campoOculto(nome: 'tipo', valor: $this->tipo);
                $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
                $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
                $this->campoOculto(nome: 'cod_servidor_curso', valor: $this->cod_servidor_curso);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo(nome: 'nm_tipo', campo: 'Tipo de Formação', valor: ($this->tipo == 'C') ? 'Curso' : 'Error');
                $this->campoTexto(nome: 'nm_formacao', campo: 'Nome do Curso', valor: $this->nm_formacao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp    = new clsFuncionario(int_idpes: $this->ref_cod_servidor);
                $detalhe    = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_(int_idpes: $detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }

                $this->campoMemo(nome: 'descricao', campo: 'Descricão', valor: $this->descricao, colunas: 60, linhas: 5);

                $this->campoRotulo(nome: 'nm_servidor', campo: 'Nome do Servidor', valor: $nm_servidor);

                $this->campoData(nome: 'data_conclusao', campo: 'Data de Conclusão', valor: $this->data_conclusao, obrigatorio: true);

                $this->campoData(nome: 'data_registro', campo: 'Data de Registro', valor: $this->data_registro);

                $this->campoMemo(
                    nome: 'diplomas_registros',
                    campo: 'Diplomas e Registros',
                    valor: $this->diplomas_registros,
                    colunas: 60,
                    linhas: 5
                );
            } elseif ($this->tipo == 'T') {
                // Primary keys
                $this->campoOculto(nome: 'cod_formacao', valor: $this->cod_formacao);
                $this->campoOculto(nome: 'tipo', valor: $this->tipo);
                $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
                $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
                $this->campoOculto(nome: 'cod_servidor_titulo', valor: $this->cod_servidor_titulo);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo(nome: 'nm_tipo', campo: 'Tipo de Formação', valor: ($this->tipo == 'T') ? 'Título' : 'Error');
                $this->campoTexto(nome: 'nm_formacao', campo: 'Nome do Título', valor: $this->nm_formacao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp     = new clsFuncionario(int_idpes: $this->ref_cod_servidor);
                $detalhe     = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_(int_idpes: $detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }

                $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);

                $this->campoRotulo(nome: 'nm_servidor', campo: 'Nome do Servidor', valor: $nm_servidor);

                $this->campoData(nome: 'data_vigencia_homolog', campo: 'Data de Vigência', valor: $this->data_vigencia_homolog, obrigatorio: true);

                $this->campoData(nome: 'data_publicacao', campo: 'Data de Publicação', valor: $this->data_publicacao, obrigatorio: true);
            } elseif ($this->tipo == 'O') {
                // Primary keys
                $this->campoOculto(nome: 'cod_formacao', valor: $this->cod_formacao);
                $this->campoOculto(nome: 'tipo', valor: $this->tipo);
                $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->ref_cod_servidor);
                $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
                $this->campoOculto(nome: 'cod_servidor_titulo', valor: $this->cod_servidor_titulo);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo(nome: 'nm_tipo', campo: 'Tipo de Formação', valor: ($this->tipo == 'O') ? 'Formação' : 'Error');
                $this->campoTexto(nome: 'nm_formacao', campo: 'Nome do Concurso', valor: $this->nm_formacao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp     = new clsFuncionario(int_idpes: $this->ref_cod_servidor);
                $detalhe     = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_(int_idpes: $detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }
                $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 60, linhas: 5);

                $this->campoRotulo(nome: 'nm_servidor', campo: 'Nome do Servidor', valor: $nm_servidor);

                $this->campoData(nome: 'data_vigencia_homolog', campo: 'Data de Homologação', valor: $this->data_vigencia_homolog, obrigatorio: true);

                $this->campoData(nome: 'data_publicacao', campo: 'Data de Publicação', valor: $this->data_publicacao, obrigatorio: true);
            }
        }
    }

    public function Novo()
    {
        $backUrl = sprintf(
            'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            cod_formacao: null,
            ref_usuario_exc: null,
            ref_usuario_cad: $this->pessoa_logada,
            ref_cod_servidor: $this->ref_cod_servidor,
            nm_formacao: $this->nm_formacao,
            tipo: $this->tipo,
            descricao: $this->descricao,
            data_cadastro: null,
            data_exclusao: null,
            ativo: $this->ativo,
            ref_ref_cod_instituicao: $this->ref_cod_instituicao
        );

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            if ($this->tipo == 'C') {
                $obj = new clsPmieducarServidorCurso(
                    cod_servidor_curso: null,
                    ref_cod_formacao: $cadastrou,
                    data_conclusao: dataToBanco(data_original: $this->data_conclusao),
                    data_registro: dataToBanco(data_original: $this->data_registro),
                    diplomas_registros: $this->diplomas_registros
                );

                if ($obj->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    $this->simpleRedirect(url: $backUrl);
                }
            } elseif ($this->tipo == 'T' || $this->tipo == 'O') {
                $obj = new clsPmieducarServidorTituloConcurso(
                    cod_servidor_titulo: null,
                    ref_cod_formacao: $cadastrou,
                    data_vigencia_homolog: dataToBanco(data_original: $this->data_vigencia_homolog),
                    data_publicacao: dataToBanco(data_original: $this->data_publicacao)
                );

                if ($obj->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    $this->simpleRedirect(url: $backUrl);
                }
            }
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $backUrl = sprintf(
            'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            cod_formacao: $this->cod_formacao,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: null,
            ref_cod_servidor: $this->ref_cod_servidor,
            nm_formacao: $this->nm_formacao,
            tipo: $this->tipo,
            descricao: $this->descricao,
            data_cadastro: null,
            data_exclusao: null,
            ativo: 1
        );

        $editou = $obj->edita();

        if ($editou) {
            if ($this->tipo == 'C') {
                $obj_curso  = new clsPmieducarServidorCurso(
                    cod_servidor_curso: $this->cod_servidor_curso,
                    ref_cod_formacao: $this->cod_formacao,
                    data_conclusao: dataToBanco(data_original: $this->data_conclusao),
                    data_registro: dataToBanco(data_original: $this->data_registro),
                    diplomas_registros: $this->diplomas_registros
                );

                $editou_cur = $obj_curso->edita();

                if ($editou_cur) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    $this->simpleRedirect(url: $backUrl);
                }
            } else {
                $obj_titulo = new clsPmieducarServidorTituloConcurso(
                    cod_servidor_titulo: $this->cod_servidor_titulo,
                    ref_cod_formacao: $this->cod_formacao,
                    data_vigencia_homolog: dataToBanco(data_original: $this->data_vigencia_homolog),
                    data_publicacao: dataToBanco(data_original: $this->data_publicacao)
                );

                $editou_tit = $obj_titulo->edita();

                if ($editou_tit) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    $this->simpleRedirect(url: $backUrl);
                }
            }
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $backUrl = sprintf(
            'educar_servidor_formacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_cod_instituicao
        );

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            cod_formacao: $this->cod_formacao,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: null,
            ref_cod_servidor: $this->ref_cod_servidor,
            nm_formacao: $this->nm_formacao,
            tipo: $this->tipo,
            descricao: $this->descricao,
            data_cadastro: null,
            data_exclusao: null,
            ativo: 0,
            ref_ref_cod_instituicao: $this->ref_cod_instituicao
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: $backUrl);
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidor Formação';
        $this->processoAp = 635;
    }
};
