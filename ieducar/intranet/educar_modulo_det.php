<?php

use App\Models\LegacyStageType;

return new class extends clsDetalhe
{
    public $cod_modulo;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_tipo;

    public $descricao;

    public $num_etapas;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Etapa - Detalhe';

        $this->cod_modulo = $_GET['cod_modulo'];

        $registro = LegacyStageType::find($this->cod_modulo);

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_modulo_lst.php');
        }
        $registro['ref_cod_instituicao'] = $registro->institution->nm_instituicao;
        $registro = $registro->getAttributes();

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(
                    detalhe: [
                        'Instituição',
                        "{$registro['ref_cod_instituicao']}",
                    ]
                );
            }
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe(
                detalhe: ['Etapa',
                    "{$registro['nm_tipo']}",
                ]
            );
        }
        if ($registro['descricao']) {
            $this->addDetalhe(
                detalhe: [
                    'Descrição',
                    "{$registro['descricao']}",
                ]
            );
        }
        $this->addDetalhe(
            detalhe: [
                'Número de etapas',
                "{$registro['num_etapas']}",
            ]
        );
        if ($registro['num_meses']) {
            $this->addDetalhe(
                detalhe: [
                    'Número de meses',
                    "{$registro['num_meses']}",
                ]
            );
        }
        if ($registro['num_semanas']) {
            $this->addDetalhe(
                detalhe: [
                    'Número de semanas',
                    "{$registro['num_semanas']}",
                ]
            );
        }
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_modulo_cad.php';
            $this->url_editar = "educar_modulo_cad.php?cod_modulo={$registro['cod_modulo']}";
        }

        $this->url_cancelar = 'educar_modulo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da etapa', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Etapa';
        $this->processoAp = '584';
    }
};
