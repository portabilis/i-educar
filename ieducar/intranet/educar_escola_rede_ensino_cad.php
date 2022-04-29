<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_escola_rede_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_rede;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_escola_rede_ensino=$_GET['cod_escola_rede_ensino'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        if (is_numeric($this->cod_escola_rede_ensino)) {
            $obj = new clsPmieducarEscolaRedeEnsino($this->cod_escola_rede_ensino);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(647, $this->pessoa_logada, 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro['cod_escola_rede_ensino']}" : 'educar_escola_rede_ensino_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' rede de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_escola_rede_ensino', $this->cod_escola_rede_ensino);

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_rede', 'Rede Ensino', $this->nm_rede, 30, 255, true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $obj = new clsPmieducarEscolaRedeEnsino(null, null, $this->pessoa_logada, $this->nm_rede, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_escola_rede_ensino_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $obj = new clsPmieducarEscolaRedeEnsino($this->cod_escola_rede_ensino, $this->pessoa_logada, null, $this->nm_rede, null, null, 1, $this->ref_cod_instituicao);
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
        $obj_permissoes->permissao_excluir(647, $this->pessoa_logada, 3, 'educar_escola_rede_ensino_lst.php');

        $obj = new clsPmieducarEscolaRedeEnsino($this->cod_escola_rede_ensino, $this->pessoa_logada, null, null, null, null, 0);
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
        $this->title = 'Escola Rede Ensino';
        $this->processoAp = '647';
    }
};
