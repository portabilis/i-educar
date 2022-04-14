<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_tipo_regime;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_tipo_regime=$_GET['cod_tipo_regime'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(568, $this->pessoa_logada, 3, 'educar_tipo_regime_lst.php');

        if (is_numeric($this->cod_tipo_regime)) {
            $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(568, $this->pessoa_logada, 3);
                //**
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_tipo_regime_det.php?cod_tipo_regime={$registro['cod_tipo_regime']}" : 'educar_tipo_regime_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de regime', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_tipo_regime', $this->cod_tipo_regime);

        // foreign keys
        // foreign keys
        $get_escola = false;
        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');
        // text
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, true);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 0, $this->ref_cod_instituicao);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Tipo Regime';
        $this->processoAp = '568';
    }
};
