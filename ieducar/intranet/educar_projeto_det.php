<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_projeto;
    public $nome;
    public $observacao;

    public function Gerar()
    {
        $this->titulo = 'Projeto - Detalhe';

        $this->cod_projeto=$_GET['cod_projeto'];

        $tmp_obj = new clsPmieducarProjeto($this->cod_projeto);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        if ($registro['cod_projeto']) {
            $this->addDetalhe([ 'C&oacute;digo projeto', "{$registro['cod_projeto']}"]);
        }
        if ($registro['nome']) {
            $this->addDetalhe([ 'Nome do projeto', "{$registro['nome']}"]);
        }
        if ($registro['observacao']) {
            $this->addDetalhe([ 'Observa&ccedil;&atilde;o', nl2br("{$registro['observacao']}")]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21250, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_projeto_cad.php';
            $this->url_editar = "educar_projeto_cad.php?cod_projeto={$registro['cod_projeto']}";
        }
        //**
        $this->url_cancelar = 'educar_projeto_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do projeto', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
