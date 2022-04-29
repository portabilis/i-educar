<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_infra_predio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $nm_predio;
    public $desc_predio;
    public $endereco;
    public $data_cadastro;
    public $data_descricao;
    public $ativo;

    public function Gerar()
    {
        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $this->titulo = 'Infra Predio - Detalhe';

        $this->cod_infra_predio=$_GET['cod_infra_predio'];

        $tmp_obj = new clsPmieducarInfraPredio($this->cod_infra_predio);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro['ref_cod_escola'] = $det_ref_cod_escola['nm_escola'];

        if ($registro['cod_infra_predio']) {
            $this->addDetalhe([ 'Infra Predio', "{$registro['cod_infra_predio']}"]);
        }
        if ($registro['ref_cod_escola']) {
            $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
        }
        if ($registro['nm_predio']) {
            $this->addDetalhe([ 'Nome Predio', "{$registro['nm_predio']}"]);
        }
        if ($registro['desc_predio']) {
            $this->addDetalhe([ 'Descrição Prédio', "{$registro['desc_predio']}"]);
        }
        if ($registro['endereco']) {
            $this->addDetalhe([ 'Endereço', "{$registro['endereco']}"]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(567, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_infra_predio_cad.php';
            $this->url_editar = "educar_infra_predio_cad.php?cod_infra_predio={$registro['cod_infra_predio']}";
        }
        //**

        $this->url_cancelar = 'educar_infra_predio_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do prédio', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Infra Predio';
        $this->processoAp = '567';
    }
};
