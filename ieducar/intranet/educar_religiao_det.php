<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_religiao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Religiao - Detalhe';

        $this->cod_religiao=$_GET['cod_religiao'];

        $tmp_obj = new clsPmieducarReligiao($this->cod_religiao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        if ($registro['cod_religiao']) {
            $this->addDetalhe([ 'Religi&atilde;o', "{$registro['cod_religiao']}"]);
        }
        if ($registro['nm_religiao']) {
            $this->addDetalhe([ 'Nome Religi&atilde;o', "{$registro['nm_religiao']}"]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(579, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_religiao_cad.php';
            $this->url_editar = "educar_religiao_cad.php?cod_religiao={$registro['cod_religiao']}";
        }
        //**

        $this->url_cancelar = 'educar_religiao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da religião', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Religiao';
        $this->processoAp = '579';
    }
};
