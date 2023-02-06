<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_pre_requisito;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $schema_;
    public $tabela;
    public $nome;
    public $sql;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Pre Requisito - Detalhe';

        $this->cod_pre_requisito=$_GET['cod_pre_requisito'];

        $tmp_obj = new clsPmieducarPreRequisito(cod_pre_requisito: $this->cod_pre_requisito);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect(url: 'educar_pre_requisito_lst.php');
        }

        $obj_ref_usuario_exc = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_exc']);
        $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
        $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

        $obj_ref_usuario_cad = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_cad']);
        $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
        $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

        if ($registro['cod_pre_requisito']) {
            $this->addDetalhe(detalhe: [ 'Pre Requisito', "{$registro['cod_pre_requisito']}"]);
        }
        if ($registro['schema_']) {
            $this->addDetalhe(detalhe: [ 'Schema ', "{$registro['schema_']}"]);
        }
        if ($registro['tabela']) {
            $this->addDetalhe(detalhe: [ 'Tabela', "{$registro['tabela']}"]);
        }
        if ($registro['nome']) {
            $this->addDetalhe(detalhe: [ 'Nome', "{$registro['nome']}"]);
        }
        if ($registro['sql']) {
            $this->addDetalhe(detalhe: [ 'Sql', "{$registro['sql']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, super_usuario: true)) {
            $this->url_novo = 'educar_pre_requisito_cad.php';
            $this->url_editar = "educar_pre_requisito_cad.php?cod_pre_requisito={$registro['cod_pre_requisito']}";
        }

        $this->url_cancelar = 'educar_pre_requisito_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Pre Requisito';
        $this->processoAp = '601';
    }
};
