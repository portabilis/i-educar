<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_habilitacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Habilitacao - Detalhe';

        $this->cod_habilitacao=$_GET['cod_habilitacao'];

        $tmp_obj = new clsPmieducarHabilitacao($this->cod_habilitacao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_habilitacao_lst.php');
        }
        if ($registro['ref_cod_instituicao']) {
            $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

            $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Habilita&ccedil;&atilde;o', "{$registro['nm_tipo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(573, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_habilitacao_cad.php';
            $this->url_editar = "educar_habilitacao_cad.php?cod_habilitacao={$registro['cod_habilitacao']}";
        }
        $this->url_cancelar = 'educar_habilitacao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da habilitação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Habilita&ccedil;&atilde;o';
        $this->processoAp = '573';
    }
};
