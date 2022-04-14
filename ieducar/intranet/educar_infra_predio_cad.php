<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_infra_predio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $nm_predio;
    public $desc_predio;
    public $endereco;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_infra_predio=$_GET['cod_infra_predio'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(567, $this->pessoa_logada, 7, 'educar_infra_predio_lst.php');

        if (is_numeric($this->cod_infra_predio)) {
            $obj = new clsPmieducarInfraPredio($this->cod_infra_predio);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(567, $this->pessoa_logada, 7);
                //**
                $retorno = 'Editar';
            } else {
                $this->simpleRedirect('educar_infra_predio_lst.php');
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_infra_predio_det.php?cod_infra_predio={$registro['cod_infra_predio']}" : 'educar_infra_predio_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' prédio', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {

        // primary keys
        $this->campoOculto('cod_infra_predio', $this->cod_infra_predio);

        $this->inputsHelper()->dynamic(['instituicao', 'escola']);

        // text
        $this->campoTexto('nm_predio', 'Nome Prédio', $this->nm_predio, 30, 255, true);
        $this->campoMemo('desc_predio', 'Descrição Prédio', $this->desc_predio, 60, 10, false);
        $this->campoMemo('endereco', 'Endereço', $this->endereco, 60, 2, true);
    }

    public function Novo()
    {
        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Infra Predio';
        $this->processoAp = '567';
    }
};
