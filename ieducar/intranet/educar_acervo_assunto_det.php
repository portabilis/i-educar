<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_acervo_assunto;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_assunto;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Acervo Assunto - Detalhe';

        $this->cod_acervo_assunto=$_GET['cod_acervo_assunto'];

        $tmp_obj = new clsPmieducarAcervoAssunto($this->cod_acervo_assunto);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_acervo_assunto_lst.php');
        }

        if ($registro['nm_assunto']) {
            $this->addDetalhe([ 'Assunto', "{$registro['nm_assunto']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_acervo_assunto_cad.php';
            $this->url_editar = "educar_acervo_assunto_cad.php?cod_acervo_assunto={$registro['cod_acervo_assunto']}";
        }

        $this->url_cancelar = 'educar_acervo_assunto_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Listagem de assuntos', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Acervo Assunto';
        $this->processoAp = '592';
    }
};
