<?php

use App\Models\LegacyTransferType;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsDetalhe
{
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

        $this->cod_transferencia_tipo = $_GET['cod_transferencia_tipo'];

        $registro = LegacyTransferType::find(id: $this->cod_transferencia_tipo)?->getAttributes();

        if (!$registro) {
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_transferencia_tipo_lst.php')
            );
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe(detalhe: ['Motivo Transferência', "{$registro['nm_tipo']}"]);
        }
        if ($registro['desc_tipo']) {
            $this->addDetalhe(detalhe: ['Descrição', "{$registro['desc_tipo']}"]);
        }

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 575, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_transferencia_tipo_cad.php';
            $this->url_editar = "educar_transferencia_tipo_cad.php?cod_transferencia_tipo={$registro['cod_transferencia_tipo']}";
        }
        $this->url_cancelar = 'educar_transferencia_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do tipo de transferência', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Transferência';
        $this->processoAp = '575';
    }
};
