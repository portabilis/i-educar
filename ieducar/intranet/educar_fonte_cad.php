<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_fonte;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_fonte;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_fonte=$_GET['cod_fonte'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(608, $this->pessoa_logada, 11, 'educar_fonte_lst.php');

        if (is_numeric($this->cod_fonte)) {
            $obj = new clsPmieducarFonte($this->cod_fonte);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(608, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_fonte_det.php?cod_fonte={$registro['cod_fonte']}" : 'educar_fonte_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' fonte', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_fonte', $this->cod_fonte);

        // foreign keys
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca']);

        // text
        $this->campoTexto('nm_fonte', 'Fonte', $this->nm_fonte, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(608, $this->pessoa_logada, 11, 'educar_fonte_lst.php');

        $obj = new clsPmieducarFonte($this->cod_fonte, $this->pessoa_logada, $this->pessoa_logada, $this->nm_fonte, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $this->cod_fonte = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_fonte = $this->cod_fonte;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_fonte_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(608, $this->pessoa_logada, 11, 'educar_fonte_lst.php');

        $obj = new clsPmieducarFonte($this->cod_fonte, $this->pessoa_logada, $this->pessoa_logada, $this->nm_fonte, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_fonte_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(608, $this->pessoa_logada, 11, 'educar_fonte_lst.php');

        $obj = new clsPmieducarFonte($this->cod_fonte, $this->pessoa_logada, $this->pessoa_logada, $this->nm_fonte, $this->descricao, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_fonte_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Fonte';
        $this->processoAp = '608';
    }
};
