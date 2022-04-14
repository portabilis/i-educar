<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_turma_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $sgl_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public function Gerar()
    {
        $this->titulo = 'Turma Tipo - Detalhe';

        $this->cod_turma_tipo=$_GET['cod_turma_tipo'];

        $tmp_obj = new clsPmieducarTurmaTipo($this->cod_turma_tipo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_turma_tipo_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }

        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Turma Tipo', "{$registro['nm_tipo']}"]);
        }
        if ($registro['sgl_tipo']) {
            $this->addDetalhe([ 'Sigla', "{$registro['sgl_tipo']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(570, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_turma_tipo_cad.php';
            $this->url_editar = "educar_turma_tipo_cad.php?cod_turma_tipo={$registro['cod_turma_tipo']}";
        }
        $this->url_cancelar = 'educar_turma_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de turma', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Turma Tipo';
        $this->processoAp = '570';
    }
};
