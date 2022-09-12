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
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        if (is_string($this->passo) && $this->passo == 1) {
            $retorno = 'Novo';
        }

        if (is_numeric($this->cod_formacao)) {
            $obj = new clsPmieducarServidorFormacao(
                $this->cod_formacao,
                null,
                null,
                $this->ref_cod_servidor,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            $registro  = $obj->detalhe();

            if ($registro) {
                $this->nm_formacao = $registro['nm_formacao'];
                $this->tipo        = $registro['tipo'];
                $this->descricao   = $registro['descricao'];

                if ($this->tipo == 'C') {
                    $obj_curso                = new clsPmieducarServidorCurso(null, $this->cod_formacao);
                    $det_curso                = $obj_curso->detalhe();
                    $this->data_conclusao     = dataFromPgToBr($det_curso['data_conclusao']);
                    $this->data_registro      = dataFromPgToBr($det_curso['data_registro']);
                    $this->diplomas_registros = $det_curso['diplomas_registros'];
                    $this->cod_servidor_curso = $det_curso['cod_servidor_curso'];
                } else {
                    $obj_outros = new clsPmieducarServidorTituloConcurso(null, $this->cod_formacao);
                    $det_outros = $obj_outros->detalhe();
                    $this->data_vigencia_homolog = dataFromPgToBr($det_outros['data_vigencia_homolog']);
                    $this->data_publicacao       = dataFromPgToBr($det_outros['data_publicacao']);
                    $this->cod_servidor_titulo   = $det_outros['cod_servidor_titulo'];
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
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
        if (! is_numeric($this->passo)) {
            $this->passo = 1;
            $this->campoOculto('passo', $this->passo);

            $opcoes = [
        'C' => 'Cursos',
        'T' => 'Títulos',
        'O' => 'Concursos'
      ];

            $this->campoLista('tipo', 'Tipo de Formação', $opcoes, $this->tipo);

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
        } elseif (is_numeric($this->passo) && $this->passo == 1) {
            if ($this->tipo == 'C') {
                // Primary keys
                $this->campoOculto('cod_formacao', $this->cod_formacao);
                $this->campoOculto('tipo', $this->tipo);
                $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
                $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
                $this->campoOculto('cod_servidor_curso', $this->cod_servidor_curso);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'C') ? 'Curso' : 'Error');
                $this->campoTexto('nm_formacao', 'Nome do Curso', $this->nm_formacao, 30, 255, true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp    = new clsFuncionario($this->ref_cod_servidor);
                $detalhe    = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }

                $this->campoMemo('descricao', 'Descricão', $this->descricao, 60, 5, false);

                $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

                $this->campoData('data_conclusao', 'Data de Conclusão', $this->data_conclusao, true);

                $this->campoData('data_registro', 'Data de Registro', $this->data_registro);

                $this->campoMemo(
                    'diplomas_registros',
                    'Diplomas e Registros',
                    $this->diplomas_registros,
                    60,
                    5,
                    false
                );
            } elseif ($this->tipo == 'T') {
                // Primary keys
                $this->campoOculto('cod_formacao', $this->cod_formacao);
                $this->campoOculto('tipo', $this->tipo);
                $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
                $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
                $this->campoOculto('cod_servidor_titulo', $this->cod_servidor_titulo);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'T') ? 'Título' : 'Error');
                $this->campoTexto('nm_formacao', 'Nome do Título', $this->nm_formacao, 30, 255, true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp     = new clsFuncionario($this->ref_cod_servidor);
                $detalhe     = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }

                $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);

                $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

                $this->campoData('data_vigencia_homolog', 'Data de Vigência', $this->data_vigencia_homolog, true);

                $this->campoData('data_publicacao', 'Data de Publicação', $this->data_publicacao, true);
            } elseif ($this->tipo == 'O') {
                // Primary keys
                $this->campoOculto('cod_formacao', $this->cod_formacao);
                $this->campoOculto('tipo', $this->tipo);
                $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
                $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
                $this->campoOculto('cod_servidor_titulo', $this->cod_servidor_titulo);

                $obrigatorio     = true;
                $get_instituicao = true;

                include 'include/pmieducar/educar_campo_lista.php';

                $this->campoRotulo('nm_tipo', 'Tipo de Formação', ($this->tipo == 'O') ? 'Formação' : 'Error');
                $this->campoTexto('nm_formacao', 'Nome do Concurso', $this->nm_formacao, 30, 255, true);

                // Foreign keys
                $nm_servidor = '';
                $objTemp     = new clsFuncionario($this->ref_cod_servidor);
                $detalhe     = $objTemp->detalhe();

                if ($detalhe) {
                    $objTmp = new clsPessoa_($detalhe['ref_cod_pessoa_fj']);
                    $det    = $objTmp->detalhe();

                    if ($det) {
                        $nm_servidor = $det['nome'];
                    }
                }
                $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);

                $this->campoRotulo('nm_servidor', 'Nome do Servidor', $nm_servidor);

                $this->campoData('data_vigencia_homolog', 'Data de Homologação', $this->data_vigencia_homolog, true);

                $this->campoData('data_publicacao', 'Data de Publicação', $this->data_publicacao, true);
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
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            null,
            null,
            $this->pessoa_logada,
            $this->ref_cod_servidor,
            $this->nm_formacao,
            $this->tipo,
            $this->descricao,
            null,
            null,
            $this->ativo,
            $this->ref_cod_instituicao
        );

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            if ($this->tipo == 'C') {
                $obj = new clsPmieducarServidorCurso(
                    null,
                    $cadastrou,
                    dataToBanco($this->data_conclusao),
                    dataToBanco($this->data_registro),
                    $this->diplomas_registros
                );

                if ($obj->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    $this->simpleRedirect($backUrl);
                }
            } elseif ($this->tipo == 'T' || $this->tipo == 'O') {
                $obj = new clsPmieducarServidorTituloConcurso(
                    null,
                    $cadastrou,
                    dataToBanco($this->data_vigencia_homolog),
                    dataToBanco($this->data_publicacao)
                );

                if ($obj->cadastra()) {
                    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                    $this->simpleRedirect($backUrl);
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
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            $this->cod_formacao,
            $this->pessoa_logada,
            null,
            $this->ref_cod_servidor,
            $this->nm_formacao,
            $this->tipo,
            $this->descricao,
            null,
            null,
            1
        );

        $editou = $obj->edita();

        if ($editou) {
            if ($this->tipo == 'C') {
                $obj_curso  = new clsPmieducarServidorCurso(
                    $this->cod_servidor_curso,
                    $this->cod_formacao,
                    dataToBanco($this->data_conclusao),
                    dataToBanco($this->data_registro),
                    $this->diplomas_registros
                );

                $editou_cur = $obj_curso->edita();

                if ($editou_cur) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    $this->simpleRedirect($backUrl);
                }
            } else {
                $obj_titulo = new clsPmieducarServidorTituloConcurso(
                    $this->cod_servidor_titulo,
                    $this->cod_formacao,
                    dataToBanco($this->data_vigencia_homolog),
                    dataToBanco($this->data_publicacao)
                );

                $editou_tit = $obj_titulo->edita();

                if ($editou_tit) {
                    $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                    $this->simpleRedirect($backUrl);
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
        $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, $backUrl);

        $obj = new clsPmieducarServidorFormacao(
            $this->cod_formacao,
            $this->pessoa_logada,
            null,
            $this->ref_cod_servidor,
            $this->nm_formacao,
            $this->tipo,
            $this->descricao,
            null,
            null,
            0,
            $this->ref_cod_instituicao
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect($backUrl);
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
