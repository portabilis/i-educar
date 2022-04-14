<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_religiao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_religiao=$_GET['cod_religiao'];

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(579, $this->pessoa_logada, 3, 'educar_religiao_lst.php');
        //**

        if (is_numeric($this->cod_religiao)) {
            $obj = new clsPmieducarReligiao($this->cod_religiao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(579, $this->pessoa_logada, 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_religiao_det.php?cod_religiao={$registro['cod_religiao']}" : 'educar_religiao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' religião', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_religiao', $this->cod_religiao);

        // foreign keys

        // text
        $this->campoTexto('nm_religiao', 'Religi&atilde;o', $this->nm_religiao, 30, 255, true);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Religiao';
        $this->processoAp = '579';
    }
};
