<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_infra_comodo_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $desc_funcao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_infra_comodo_funcao=$_GET['cod_infra_comodo_funcao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(572, $this->pessoa_logada, 7, 'educar_infra_comodo_funcao_lst.php');

        if (is_numeric($this->cod_infra_comodo_funcao)) {
            $obj = new clsPmieducarInfraComodoFuncao();
            $lst  = $obj->lista($this->cod_infra_comodo_funcao);
            if (is_array($lst)) {
                $registro = array_shift($lst);
                if ($registro) {
                    foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                        $this->$campo = $val;
                    }

                    //** verificao de permissao para exclusao
                    $this->fexcluir = $obj_permissoes->permissao_excluir(572, $this->pessoa_logada, 7);
                    //**

                    $retorno = 'Editar';
                } else {
                    $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
                }
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro['cod_infra_comodo_funcao']}" : 'educar_infra_comodo_funcao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_infra_comodo_funcao', $this->cod_infra_comodo_funcao);

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        // text
        $this->campoTexto('nm_funcao', 'Tipo', $this->nm_funcao, 30, 255, true);
        $this->campoMemo('desc_funcao', 'Descri&ccedil;&atilde;o do tipo', $this->desc_funcao, 60, 5, false);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarInfraComodoFuncao(null, null, $this->pessoa_logada, $this->nm_funcao, $this->desc_funcao, null, null, 1, $this->ref_cod_escola);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarInfraComodoFuncao($this->cod_infra_comodo_funcao, $this->pessoa_logada, null, $this->nm_funcao, $this->desc_funcao, null, null, 1, $this->ref_cod_escola);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarInfraComodoFuncao($this->cod_infra_comodo_funcao, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Tipo de ambiente';
        $this->processoAp = '572';
    }
};
