<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_raca;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_raca;
    public $data_cadastro;
    public $data_exclusao;
    public $raca_educacenso;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_raca=$_GET['cod_raca'];

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7, 'educar_raca_lst.php');

        if (is_numeric($this->cod_raca)) {
            $obj = new clsCadastroRaca($this->cod_raca);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->fexcluir = $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7);

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_raca_det.php?cod_raca={$registro['cod_raca']}" : 'educar_raca_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' raça', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_raca', $this->cod_raca);

        $this->campoTexto('nm_raca', 'Ra&ccedil;a', $this->nm_raca, 30, 255, true);

        $resources = [  0 => 'Não declarada',
                                1 => 'Branca',
                                2 => 'Preta',
                                3 => 'Parda',
                                4 => 'Amarela',
                                5 => 'Indígena'];

        $options = ['label' => 'Raça educacenso', 'resources' => $resources, 'value' => $this->raca_educacenso];
        $this->inputsHelper()->select('raca_educacenso', $options);
    }

    public function Novo()
    {
        $obj = new clsCadastroRaca($this->cod_raca, null, $this->pessoa_logada, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $obj->raca_educacenso = $this->raca_educacenso;
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $obj->raca_educacenso = $this->raca_educacenso;
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, 0);

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Ra&ccedil;a';
        $this->processoAp = '678';
    }
};
