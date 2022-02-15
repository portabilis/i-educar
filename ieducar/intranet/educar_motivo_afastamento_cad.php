<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada = null;

    public $cod_motivo_afastamento = null;
    public $ref_usuario_exc        = null;
    public $ref_usuario_cad        = null;
    public $nm_motivo              = null;
    public $descricao              = null;
    public $data_cadastro          = null;
    public $data_exclusao          = null;
    public $ativo                  = null;
    public $ref_cod_instituicao    = null;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_motivo_afastamento = $_GET['cod_motivo_afastamento'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(633, $this->pessoa_logada, 7, 'educar_motivo_afastamento_lst.php');

        if (is_numeric($this->cod_motivo_afastamento)) {
            $obj = new clsPmieducarMotivoAfastamento($this->cod_motivo_afastamento);
            $registro  = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {    // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
                $det_escola = $obj_escola->detalhe();
                $this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

                if ($obj_permissoes->permissao_excluir(633, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
                $this->ref_cod_instituicao = $registro['ref_cod_instituicao'];
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}" : 'educar_motivo_afastamento_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' motivo de afastamento', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_motivo_afastamento', $this->cod_motivo_afastamento);

        // foreign keys
        $obrigatorio = true;
        $get_escola = false;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_motivo', 'Motivo de Afastamento', $this->nm_motivo, 30, 255, true);
        $this->campoMemo('descricao', 'Descrição', $this->descricao, 60, 5, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(633, $this->pessoa_logada, 7, 'educar_motivo_afastamento_lst.php');

        $obj = new clsPmieducarMotivoAfastamento(null, null, $this->pessoa_logada, $this->nm_motivo, $this->descricao, null, null, 1, $this->ref_cod_instituicao);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            633,
            $this->pessoa_logada,
            7,
            'educar_motivo_afastamento_lst.php'
        );

        $obj = new clsPmieducarMotivoAfastamento(
            $this->cod_motivo_afastamento,
            $this->pessoa_logada,
            null,
            $this->nm_motivo,
            $this->descricao,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(633, $this->pessoa_logada, 7, 'educar_motivo_afastamento_lst.php');

        $obj = new clsPmieducarMotivoAfastamento(
            $this->cod_motivo_afastamento,
            $this->pessoa_logada,
            null,
            $this->nm_motivo,
            $this->descricao,
            null,
            null,
            0,
            $this->ref_cod_instituicao
        );

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_motivo_afastamento_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Motivo Afastamento';
        $this->processoAp = '633';
    }
};
