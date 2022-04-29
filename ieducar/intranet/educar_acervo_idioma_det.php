<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_acervo_idioma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_idioma;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Idioma - Detalhe';

        $this->cod_acervo_idioma=$_GET['cod_acervo_idioma'];

        $tmp_obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        if ($registro['cod_acervo_idioma']) {
            $this->addDetalhe([ 'Código Idioma', "{$registro['cod_acervo_idioma']}"]);
        }
        if ($registro['nm_idioma']) {
            $this->addDetalhe([ 'Idioma', "{$registro['nm_idioma']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(590, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_acervo_idioma_cad.php';
            $this->url_editar = "educar_acervo_idioma_cad.php?cod_acervo_idioma={$registro['cod_acervo_idioma']}";
        }

        $this->url_cancelar = 'educar_acervo_idioma_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do idioma', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Idioma';
        $this->processoAp = '590';
    }
};
