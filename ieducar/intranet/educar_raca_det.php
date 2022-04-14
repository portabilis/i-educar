<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_raca;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_raca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $pessoa_logada;

    public function Gerar()
    {
        $this->titulo = 'Ra&ccedil;a - Detalhe';

        $this->cod_raca=$_GET['cod_raca'];

        $tmp_obj = new clsCadastroRaca($this->cod_raca);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_raca_lst.php');
        }

        if ($registro['nm_raca']) {
            $this->addDetalhe([ 'Ra&ccedil;a', "{$registro['nm_raca']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_raca_cad.php';
            $this->url_editar = "educar_raca_cad.php?cod_raca={$registro['cod_raca']}";
        }

        $this->url_cancelar = 'educar_raca_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da raça', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ra&ccedil;a';
        $this->processoAp = '678';
    }
};
