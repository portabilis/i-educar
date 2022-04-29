<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_motivo_baixa;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo_baixa;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Motivo Baixa - Detalhe';

        $this->cod_motivo_baixa=$_GET['cod_motivo_baixa'];

        $tmp_obj = new clsPmieducarMotivoBaixa($this->cod_motivo_baixa);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_motivo_baixa_lst.php');
        }

        if ($registro['nm_motivo_baixa']) {
            $this->addDetalhe([ 'Motivo Baixa', "{$registro['nm_motivo_baixa']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(600, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_motivo_baixa_cad.php';
            $this->url_editar = "educar_motivo_baixa_cad.php?cod_motivo_baixa={$registro['cod_motivo_baixa']}";
        }

        $this->url_cancelar = 'educar_motivo_baixa_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do motivo de baixa', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Baixa';
        $this->processoAp = '600';
    }
};
