<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_coffebreak_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $custo_unitario;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(564, $this->pessoa_logada, 7, 'educar_coffebreak_tipo_lst.php');
        //**

        $this->cod_coffebreak_tipo=$_GET['cod_coffebreak_tipo'];

        if (is_numeric($this->cod_coffebreak_tipo)) {
            $obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(564, $this->pessoa_logada, 7);
                //**
                $retorno = 'Editar';
            } else {
                $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_coffebreak_tipo_det.php?cod_coffebreak_tipo={$registro['cod_coffebreak_tipo']}" : 'educar_coffebreak_tipo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_coffebreak_tipo', $this->cod_coffebreak_tipo);

        // foreign keys

        // text
        $this->campoTexto('nm_tipo', 'Nome Coffee Break', $this->nm_tipo, 42, 255, true);
        $this->campoMonetario('custo_unitario', 'Custo Unit&aacute;rio', $this->custo_unitario, 15, 255, true);
        $this->campoMemo('desc_tipo', 'Descri&ccedil;&atilde;o Coffee Break', $this->desc_tipo, 40, 10, false);

        // data
    }

    public function Novo()
    {
        $this->custo_unitario = str_replace('.', '', $this->custo_unitario);
        $this->custo_unitario = str_replace(',', '.', $this->custo_unitario);

        $obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo, null, $this->pessoa_logada, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null, null, 1);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->custo_unitario = str_replace('.', '', $this->custo_unitario);
        $this->custo_unitario = str_replace(',', '.', $this->custo_unitario);

        $obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->desc_tipo, $this->custo_unitario, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Coffebreak Tipo';
        $this->processoAp = '564';
    }
};
