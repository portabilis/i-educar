<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_motivo_suspensao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Motivo Suspensão - Detalhe';

        $this->cod_motivo_suspensao=$_GET['cod_motivo_suspensao'];

        $tmp_obj = new clsPmieducarMotivoSuspensao($this->cod_motivo_suspensao);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_motivo_suspensao_lst.php');
        }

        if ($registro['nm_motivo']) {
            $this->addDetalhe([ 'Motivo Suspensão', "{$registro['nm_motivo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descrição', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(607, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_motivo_suspensao_cad.php';
            $this->url_editar = "educar_motivo_suspensao_cad.php?cod_motivo_suspensao={$registro['cod_motivo_suspensao']}";
        }

        $this->url_cancelar = 'educar_motivo_suspensao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do motivo de suspensão', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Suspensão';
        $this->processoAp = '607';
    }
};
