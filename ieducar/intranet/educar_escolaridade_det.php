<?php

return new class extends clsDetalhe {
    /**
     * Referência a usuário da sessão
     *
     * @var int
     */
    public $pessoa_logada = null;

    /**
     * Título no topo da página
     *
     * @var string
     */
    public $titulo = '';

    public $idesco;
    public $descricao;

    public function Gerar()
    {
        $this->titulo = 'Escolaridade - Detalhe';

        $this->idesco = $_GET['idesco'];

        $tmp_obj = new clsCadastroEscolaridade($this->idesco);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_escolaridade_lst.php');
        }

        if ($registro['descricao']) {
            $this->addDetalhe(['Descrição', $registro['descricao']]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3)) {
            $this->url_novo   = 'educar_escolaridade_cad.php';
            $this->url_editar = 'educar_escolaridade_cad.php?idesco=' . $registro['idesco'];
        }

        $this->url_cancelar = 'educar_escolaridade_lst.php';
        $this->largura      = '100%';

        $this->breadcrumb('Detalhe da escolaridade', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Escolaridade';
        $this->processoAp = '632';
    }
};
