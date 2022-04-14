<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acervo_autor;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_autor;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_acervo_autor=$_GET['cod_acervo_autor'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11, 'educar_acervo_autor_lst.php');

        if (is_numeric($this->cod_acervo_autor)) {
            $obj = new clsPmieducarAcervoAutor($this->cod_acervo_autor);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->nm_autor = stripslashes($this->nm_autor);
                $this->nm_autor = htmlspecialchars($this->nm_autor);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(594, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $this->ref_cod_instituicao = $det_ref_cod_biblioteca['ref_cod_instituicao'];
                $this->ref_cod_escola = $det_ref_cod_biblioteca['ref_cod_escola'];

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_acervo_autor_det.php?cod_acervo_autor={$registro['cod_acervo_autor']}" : 'educar_acervo_autor_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' autor', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_acervo_autor', $this->cod_acervo_autor);

        // foreign keys
        $get_escola     = 1;
        $escola_obrigatorio = false;
        $get_biblioteca = 1;
        $instituicao_obrigatorio = true;
        $biblioteca_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_autor', 'Autor', $this->nm_autor, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11, 'educar_acervo_autor_lst.php');

        $obj = new clsPmieducarAcervoAutor(null, null, $this->pessoa_logada, $this->nm_autor, $this->descricao, null, null, 1, $this->ref_cod_biblioteca);
        $this->cod_acervo_autor = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo_autor = $this->cod_acervo_autor;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_autor_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(594, $this->pessoa_logada, 11, 'educar_acervo_autor_lst.php');

        $obj = new clsPmieducarAcervoAutor($this->cod_acervo_autor, $this->pessoa_logada, null, $this->nm_autor, $this->descricao, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $obj->cod_acervo_autor = $this->cod_acervo_autor;
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_autor_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(594, $this->pessoa_logada, 11, 'educar_acervo_autor_lst.php');

        $obj = new clsPmieducarAcervoAutor($this->cod_acervo_autor, $this->pessoa_logada, null, null, null, null, null, 0, $this->ref_cod_biblioteca);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

            $this->simpleRedirect('educar_acervo_assunto_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Autor';
        $this->processoAp = '594';
    }
};
