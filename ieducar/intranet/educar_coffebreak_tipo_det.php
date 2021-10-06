<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_coffebreak_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $custo_unitario;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Coffebreak Tipo - Detalhe';

        $this->cod_coffebreak_tipo=$_GET['cod_coffebreak_tipo'];

        $tmp_obj = new clsPmieducarCoffebreakTipo($this->cod_coffebreak_tipo);
        $registro = $tmp_obj->detalhe();

        if (! $registro || !$registro['ativo']) {
            $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
        }

        if ($registro['cod_coffebreak_tipo']) {
            $this->addDetalhe([ 'Coffebreak Tipo', "{$registro['cod_coffebreak_tipo']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Nome Tipo', "{$registro['nm_tipo']}"]);
        }
        if ($registro['desc_tipo']) {
            $this->addDetalhe([ 'Desc Tipo', "{$registro['desc_tipo']}"]);
        }
        if ($registro['custo_unitario']) {
            $this->addDetalhe([ 'Custo Unitario', str_replace('.', ',', $registro['custo_unitario'])]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_coffebreak_tipo_cad.php';
            $this->url_editar = "educar_coffebreak_tipo_cad.php?cod_coffebreak_tipo={$registro['cod_coffebreak_tipo']}";
        }
        //**

        $this->url_cancelar = 'educar_coffebreak_tipo_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->titulo = 'i-Educar - Coffebreak Tipo';
        $this->processoAp = '564';
    }
};
