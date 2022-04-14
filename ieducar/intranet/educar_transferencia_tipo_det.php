<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $desc_tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Transferencia Tipo - Detalhe';

        $this->cod_transferencia_tipo=$_GET['cod_transferencia_tipo'];

        $tmp_obj = new clsPmieducarTransferenciaTipo($this->cod_transferencia_tipo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            throw new HttpResponseException(
                new RedirectResponse('educar_transferencia_tipo_lst.php')
            );
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
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Motivo Transfer&ecirc;ncia', "{$registro['nm_tipo']}"]);
        }
        if ($registro['desc_tipo']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['desc_tipo']}"]);
        }

        if ($obj_permissoes->permissao_cadastra(575, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_transferencia_tipo_cad.php';
            $this->url_editar = "educar_transferencia_tipo_cad.php?cod_transferencia_tipo={$registro['cod_transferencia_tipo']}";
        }
        $this->url_cancelar = 'educar_transferencia_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do tipo de transferência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Transfer&ecirc;ncia';
        $this->processoAp = '575';
    }
};
