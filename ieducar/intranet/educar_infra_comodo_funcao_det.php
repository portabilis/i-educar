<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_infra_comodo_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $desc_funcao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Tipo de ambiente  - Detalhe';

        $this->cod_infra_comodo_funcao=$_GET['cod_infra_comodo_funcao'];

//      $tmp_obj = new clsPmieducarInfraComodoFuncao( $this->cod_infra_comodo_funcao );
//      $registro = $tmp_obj->detalhe();
        $obj = new clsPmieducarInfraComodoFuncao();
        $lst  = $obj->lista($this->cod_infra_comodo_funcao);
        if (is_array($lst)) {
            $registro = array_shift($lst);
        }

        if (! $registro) {
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($nm_escola) {
                $this->addDetalhe([ 'Escola', "{$nm_escola}"]);
            }
        }
        if ($registro['cod_infra_comodo_funcao']) {
            $this->addDetalhe([ 'Código tipo de ambiente', "{$registro['cod_infra_comodo_funcao']}"]);
        }
        if ($registro['nm_funcao']) {
            $this->addDetalhe([ 'Tipo de ambiente', "{$registro['nm_funcao']}"]);
        }
        if ($registro['desc_funcao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o do tipo', "{$registro['desc_funcao']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(572, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_infra_comodo_funcao_cad.php';
            $this->url_editar = "educar_infra_comodo_funcao_cad.php?cod_infra_comodo_funcao={$registro['cod_infra_comodo_funcao']}";
        }

        $this->url_cancelar = 'educar_infra_comodo_funcao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Tipo de ambiente';
        $this->processoAp = '572';
    }
};
