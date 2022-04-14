<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_deficiencia;
    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Defici&ecirc;ncia - Detalhe';

        $this->cod_deficiencia=$_GET['cod_deficiencia'];

        $tmp_obj = new clsCadastroDeficiencia($this->cod_deficiencia);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        if ($registro['nm_deficiencia']) {
            $this->addDetalhe([ 'Deficiência', "{$registro['nm_deficiencia']}"]);
        }
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(631, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_deficiencia_cad.php';
            $this->url_editar = "educar_deficiencia_cad.php?cod_deficiencia={$registro['cod_deficiencia']}";
        }
        $this->url_cancelar = 'educar_deficiencia_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da deficiência', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Defici&ecirc;ncia';
        $this->processoAp = '631';
    }
};
