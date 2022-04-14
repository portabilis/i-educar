<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_tipo_regime;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Tipo Regime - Detalhe';

        $this->cod_tipo_regime=$_GET['cod_tipo_regime'];

        $tmp_obj = new clsPmieducarTipoRegime($this->cod_tipo_regime);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        if ($registro['cod_tipo_regime']) {
            $this->addDetalhe([ 'Tipo Regime', "{$registro['cod_tipo_regime']}"]);
        }
        if ($registro['ref_cod_instituicao']) {
            $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

            $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Nome Tipo', "{$registro['nm_tipo']}"]);
        }

        $this->url_cancelar = 'educar_tipo_regime_lst.php';

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(568, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_tipo_regime_cad.php';
            $this->url_editar = "educar_tipo_regime_cad.php?cod_tipo_regime={$registro['cod_tipo_regime']}";
        }
        //**
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de regime', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo Regime';
        $this->processoAp = '568';
    }
};
