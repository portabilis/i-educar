<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_transferencia_tipo=$_GET['cod_transferencia_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(575, $this->pessoa_logada, 7, 'educar_transferencia_tipo_lst.php');

        if (is_numeric($this->cod_transferencia_tipo)) {
            $obj = new clsPmieducarTransferenciaTipo();
            $lst  = $obj->lista($this->cod_transferencia_tipo);
            $registro  = array_shift($lst);
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(575, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro['cod_transferencia_tipo']}" : 'educar_transferencia_tipo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de transferência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_transferencia_tipo', $this->cod_transferencia_tipo);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_tipo', 'Motivo Transfer&ecirc;ncia', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('desc_tipo', 'Descri&ccedil;&atilde;o', $this->desc_tipo, 60, 5, false);
    }

    public function Novo()
    {
        $obj = new clsPmieducarTransferenciaTipo(null, null, $this->pessoa_logada, $this->nm_tipo, $this->desc_tipo, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarTransferenciaTipo($this->cod_transferencia_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->desc_tipo, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarTransferenciaTipo($this->cod_transferencia_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Motivo Transfer&ecirc;ncia';
        $this->processoAp = '575';
    }
};
