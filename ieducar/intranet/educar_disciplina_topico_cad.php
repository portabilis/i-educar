<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_disciplina_topico;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_topico;
    public $desc_topico;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_disciplina_topico=$_GET['cod_disciplina_topico'];

        if (is_numeric($this->cod_disciplina_topico)) {
            $obj = new clsPmieducarDisciplinaTopico($this->cod_disciplina_topico);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $obj_permissao = new clsPermissoes();
                $this->fexcluir = $obj_permissao->permissao_excluir(565, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_disciplina_topico_det.php?cod_disciplina_topico={$registro['cod_disciplina_topico']}" : 'educar_disciplina_topico_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(565, $this->pessoa_logada, 7, 'educar_disciplina_topico_lst.php');
        // primary keys
        $this->campoOculto('cod_disciplina_topico', $this->cod_disciplina_topico);

        // foreign keys

        // text
        $this->campoTexto('nm_topico', 'Nome T&oacute;pico', $this->nm_topico, 30, 255, true);
        $this->campoMemo('desc_topico', 'Descri&ccedil;&atilde;o T&oacute;pico', $this->desc_topico, 30, 5, false);

        // data
    }

    public function Novo()
    {
        $obj = new clsPmieducarDisciplinaTopico(null, null, $this->pessoa_logada, $this->nm_topico, $this->desc_topico);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_disciplina_topico_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarDisciplinaTopico($this->cod_disciplina_topico, $this->pessoa_logada, null, $this->nm_topico, $this->desc_topico, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_disciplina_topico_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarDisciplinaTopico($this->cod_disciplina_topico, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_disciplina_topico_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Disciplina T&oacute;pico';
        $this->processoAp = '565';
    }
};
