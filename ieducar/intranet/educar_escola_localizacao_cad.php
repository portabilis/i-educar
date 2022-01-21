<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_escola_localizacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_localizacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $this->cod_escola_localizacao=$_GET['cod_escola_localizacao'];

        if (is_numeric($this->cod_escola_localizacao)) {
            $obj = new clsPmieducarEscolaLocalizacao($this->cod_escola_localizacao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = true;
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_escola_localizacao_det.php?cod_escola_localizacao={$registro['cod_escola_localizacao']}" : 'educar_escola_localizacao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' localização', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_escola_localizacao', $this->cod_escola_localizacao);

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_localizacao', 'Localização', $this->nm_localizacao, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $obj = new clsPmieducarEscolaLocalizacao(null, null, $this->pessoa_logada, $this->nm_localizacao, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $obj = new clsPmieducarEscolaLocalizacao($this->cod_escola_localizacao, $this->pessoa_logada, null, $this->nm_localizacao, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(562, $this->pessoa_logada, 3, 'educar_escola_localizacao_lst.php');

        $obj = new clsPmieducarEscolaLocalizacao($this->cod_escola_localizacao, $this->pessoa_logada, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Escola Localização';
        $this->processoAp = '562';
    }
};
