<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_projeto;
    public $nome;
    public $observacao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_projeto=$_GET['cod_projeto'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(21250, $this->pessoa_logada, 3, 'educar_projeto_lst.php');

        if (is_numeric($this->cod_projeto)) {
            $obj = new clsPmieducarProjeto($this->cod_projeto);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(21250, $this->pessoa_logada, 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}" : 'educar_projeto_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' projeto', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_projeto', $this->cod_projeto);

        // foreign keys

        // text
        $this->campoTexto('nome', 'Nome do projeto', $this->nome, 50, 50, true);
        $this->campoMemo('observacao', 'Observa&ccedil;&atilde;o', $this->observacao, 52, 5, false);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarProjeto(null, $this->nome, $this->observacao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarProjeto($this->cod_projeto, $this->nome, $this->observacao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarProjeto($this->cod_projeto);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
