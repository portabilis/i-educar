<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $id;
    public $descricao;
    public $observacoes;

    public function Gerar()
    {
        $this->titulo = 'Categoria Obras - Detalhe';

        $this->id = $_GET['id'];

        $tmp_obj = new clsPmieducarCategoriaObra($this->id);
        $registro = $tmp_obj->detalhe();
        if (!$registro) {
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }
        if ($registro['id']) {
            $this->addDetalhe(['C&oacute;digo', "{$registro['id']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(['Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }
        if ($registro['observacoes']) {
            $this->addDetalhe(['Observa&ccedil;&otilde;es', "{$registro['observacoes']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_categoria_cad.php';
            $this->url_editar = "educar_categoria_cad.php?id={$registro['id']}";
        }

        $this->url_cancelar = 'educar_categoria_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Listagem de categorias', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Categoria Obras';
        $this->processoAp = 599;
    }
};
