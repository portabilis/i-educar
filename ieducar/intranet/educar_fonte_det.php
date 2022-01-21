<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_fonte;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_fonte;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Fonte - Detalhe';

        $this->cod_fonte=$_GET['cod_fonte'];

        $tmp_obj = new clsPmieducarFonte($this->cod_fonte);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_fonte_lst.php');
        }

        if ($registro['cod_fonte']) {
            $this->addDetalhe([ 'Código Fonte', "{$registro['cod_fonte']}"]);
        }
        if ($registro['nm_fonte']) {
            $this->addDetalhe([ 'Fonte', "{$registro['nm_fonte']}"]);
        }
        if ($registro['descricao']) {
            $registro['descricao'] = nl2br($registro['descricao']);
            $this->addDetalhe([ 'Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(608, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_fonte_cad.php';
            $this->url_editar = "educar_fonte_cad.php?cod_fonte={$registro['cod_fonte']}";
        }

        $this->url_cancelar = 'educar_fonte_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da fonte', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Fonte';
        $this->processoAp = '608';
    }
};
