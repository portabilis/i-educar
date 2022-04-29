<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_motivo_suspensao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_motivo_suspensao=$_GET['cod_motivo_suspensao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(607, $this->pessoa_logada, 11, 'educar_motivo_suspensao_lst.php');

        if (is_numeric($this->cod_motivo_suspensao)) {
            $obj = new clsPmieducarMotivoSuspensao($this->cod_motivo_suspensao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(607, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_motivo_suspensao_det.php?cod_motivo_suspensao={$registro['cod_motivo_suspensao']}" : 'educar_motivo_suspensao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' motivo de suspensão', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_motivo_suspensao', $this->cod_motivo_suspensao);

        //foreign keys
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'biblioteca']);

        // text
        $this->campoTexto('nm_motivo', 'Motivo Suspensão', $this->nm_motivo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(607, $this->pessoa_logada, 11, 'educar_motivo_suspensao_lst.php');

        $obj = new clsPmieducarMotivoSuspensao(null, null, $this->pessoa_logada, $this->nm_motivo, $this->descricao, null, null, 1, $this->ref_cod_biblioteca);
        $this->cod_motivo_suspensao = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_motivo_suspensao = $this->cod_motivo_suspensao;
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_suspensao_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(607, $this->pessoa_logada, 11, 'educar_motivo_suspensao_lst.php');

        $obj = new clsPmieducarMotivoSuspensao($this->cod_motivo_suspensao, $this->pessoa_logada, null, $this->nm_motivo, $this->descricao, null, null, 1, $this->ref_cod_biblioteca);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_suspensao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(607, $this->pessoa_logada, 11, 'educar_motivo_suspensao_lst.php');

        $obj = new clsPmieducarMotivoSuspensao($this->cod_motivo_suspensao, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_suspensao_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Motivo Suspensão';
        $this->processoAp = '607';
    }
};
