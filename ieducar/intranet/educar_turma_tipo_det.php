<?php

use App\Models\LegacySchoolClassType;

return new class extends clsDetalhe
{
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

        $this->cod_turma_tipo = $_GET['cod_turma_tipo'];

        $registro = LegacySchoolClassType::find(id: $this->cod_turma_tipo)->getAttributes();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_turma_tipo_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }

        if ($registro['nm_tipo']) {
            $this->addDetalhe(detalhe: ['Turma Tipo', "{$registro['nm_tipo']}"]);
        }
        if ($registro['sgl_tipo']) {
            $this->addDetalhe(detalhe: ['Sigla', "{$registro['sgl_tipo']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 570, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_turma_tipo_cad.php';
            $this->url_editar = "educar_turma_tipo_cad.php?cod_turma_tipo={$registro['cod_turma_tipo']}";
        }
        $this->url_cancelar = 'educar_turma_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do tipo de turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Turma Tipo';
        $this->processoAp = '570';
    }
};
