<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

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
    public $ref_cod_biblioteca_;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_situacao=$_GET['cod_situacao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(602, $this->pessoa_logada, 11, 'educar_situacao_lst.php');

        $this->ref_cod_biblioteca = $this->ref_cod_biblioteca_ = $obj_permissoes->getBiblioteca($this->pessoa_logada);
        if (is_numeric($this->cod_situacao)) {
            $obj = new clsPmieducarSituacao($this->cod_situacao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($this->cod_situacao) {
                    $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                    $det_biblioteca = $obj_biblioteca->detalhe();
                    $this->ref_cod_instituicao = $det_biblioteca['ref_cod_instituicao'];
                    $this->ref_cod_escola = $det_biblioteca['ref_cod_escola'];
                    $this->ref_cod_biblioteca = $this->ref_cod_biblioteca_ = $this->ref_cod_biblioteca;
                }

                if ($obj_permissoes->permissao_excluir(602, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_situacao_det.php?cod_situacao={$registro['cod_situacao']}" : 'educar_situacao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' situação', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_situacao', $this->cod_situacao);

        try {
            $get_escola     = 1;
            $escola_obrigatorio = false;
            $get_biblioteca = 1;
            $instituicao_obrigatorio = true;
            $biblioteca_obrigatorio = true;
            include('include/pmieducar/educar_campo_lista.php');
        } catch (Exception $exception) {
            $this->mensagem = $exception->getMessage();
            $this->simpleRedirect('educar_situacao_lst.php');
            return false;
        }

        $this->campoTexto('nm_situacao', 'Situação', $this->nm_situacao, 30, 255, true);

        $opcoes = ['' => 'Selecione', 1 => 'não', 2 => 'sim' ];
        $this->campoLista('permite_emprestimo', 'Permite Empréstimo', $opcoes, $this->permite_emprestimo);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5);

        $obj_situacao = new clsPmieducarSituacao();
        $lst_situacao = false;
        if ($this->ref_cod_biblioteca_) {
            $lst_situacao = $obj_situacao->lista(null, null, null, null, null, null, 1, null, null, null, null, null, 1, $this->ref_cod_biblioteca_, null, null);
        }

        $script = '';
        if ($lst_situacao) {

            $achou = false;
            $script = '';
            foreach ($lst_situacao as $situacao) {
                if ($situacao['cod_situacao'] == $this->cod_situacao) {
                    $achou = true;
                }
            }

            if (!$achou) {
                $script .= "setVisibility('tr_situacao_padrao',false);\n";
            }
        }
        $this->campoCheck('situacao_padrao', 'Situação Padrão', $this->situacao_padrao);

        $obj_situacao = new clsPmieducarSituacao();
        if ($this->ref_cod_biblioteca_) {
            $lst_situacao = $obj_situacao->lista(null, null, null, null, null, null, null, 1, null, null, null, null, 1, $this->ref_cod_biblioteca_, null, null);
        }

        if ($lst_situacao) {
            $achou = false;
            foreach ($lst_situacao as $situacao) {
                if ($situacao['cod_situacao'] == $this->cod_situacao) {
                    $achou = true;
                }
            }
            if (!$achou) {
                $script .="setVisibility('tr_situacao_emprestada',false);\n";
            }
        }

        if (! empty($script)) {
            echo "<script>window.onload=function(){{$script}}</script>";
        }
        $this->campoCheck('situacao_emprestada', 'Situa&ccedil;&atilde;o Emprestada', $this->situacao_emprestada);
        $this->acao_enviar = 'valida()';
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(602, $this->pessoa_logada, 11, 'educar_situacao_lst.php');

        $this->situacao_padrao = is_null($this->situacao_padrao) ? 0 : 1;
        $this->situacao_emprestada = is_null($this->situacao_emprestada) ? 0 : 1;

        $obj = new clsPmieducarSituacao(null, null, $this->pessoa_logada, $this->nm_situacao, $this->permite_emprestimo, $this->descricao, $this->situacao_padrao, $this->situacao_emprestada, null, null, 1, $this->ref_cod_biblioteca);
        $this->cod_situacao = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_situacao = $this->cod_situacao;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_situacao_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(602, $this->pessoa_logada, 11, 'educar_situacao_lst.php');

        $this->situacao_padrao = is_null($this->situacao_padrao) ? 0 : 1;
        $this->situacao_emprestada = is_null($this->situacao_emprestada) ? 0 : 1;

        $obj = new clsPmieducarSituacao($this->cod_situacao, $this->pessoa_logada, null, $this->nm_situacao, $this->permite_emprestimo, $this->descricao, $this->situacao_padrao, $this->situacao_emprestada, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_situacao_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(602, $this->pessoa_logada, 11, 'educar_situacao_lst.php');

        $obj = new clsPmieducarSituacao($this->cod_situacao, $this->pessoa_logada, null, null, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_situacao_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-situacao-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Situação';
        $this->processoAp = '602';
    }
};
