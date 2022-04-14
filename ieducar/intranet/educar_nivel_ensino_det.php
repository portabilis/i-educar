<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_nivel_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_nivel;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'N&iacute;vel Ensino - Detalhe';

        $this->cod_nivel_ensino=$_GET['cod_nivel_ensino'];

        $tmp_obj = new clsPmieducarNivelEnsino($this->cod_nivel_ensino);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_nivel_ensino_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nm_nivel']) {
            $this->addDetalhe([ 'N&iacute;vel Ensino', "{$registro['nm_nivel']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        if ($obj_permissoes->permissao_cadastra(571, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_nivel_ensino_cad.php';
            $this->url_editar = "educar_nivel_ensino_cad.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}";
        }
        $this->url_cancelar = 'educar_nivel_ensino_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do nível de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Nivel Ensino';
        $this->processoAp = '571';
    }
};
