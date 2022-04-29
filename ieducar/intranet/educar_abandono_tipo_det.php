<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_abandono_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Abandono Tipo - Detalhe';

        $this->cod_abandono_tipo=$_GET['cod_abandono_tipo'];

        $tmp_obj = new clsPmieducarAbandonoTipo($this->cod_abandono_tipo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe([ 'Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nome']) {
            $this->addDetalhe([ 'Motivo Abandono', "{$registro['nome']}"]);
        }
        if ($obj_permissoes->permissao_cadastra(950, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_abandono_tipo_cad.php';
            $this->url_editar = "educar_abandono_tipo_cad.php?cod_abandono_tipo={$registro['cod_abandono_tipo']}";
        }
        $this->url_cancelar = 'educar_abandono_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de abandono', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Abandono';
        $this->processoAp = '950';
    }
};
