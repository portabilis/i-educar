<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_acervo_idioma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_idioma;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_acervo_idioma=$_GET['cod_acervo_idioma'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        if (is_numeric($this->cod_acervo_idioma)) {
            $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(590, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_acervo_idioma_det.php?cod_acervo_idioma={$registro['cod_acervo_idioma']}" : 'educar_acervo_idioma_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' idioma', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_acervo_idioma', $this->cod_acervo_idioma);

        //foreign keys
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca']);

        // text
        $this->campoTexto('nm_idioma', 'Idioma', $this->nm_idioma, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $this->cod_acervo_idioma = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_acervo_idioma = $this->cod_acervo_idioma;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(590, $this->pessoa_logada, 11, 'educar_acervo_idioma_lst.php');

        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Idioma';
        $this->processoAp = '590';
    }
};
