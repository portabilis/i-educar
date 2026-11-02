<?php

use App\Models\LegacyAbandonmentType;

return new class extends clsDetalhe
{
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
        $this->titulo = 'Deixou de Frequentar Tipo - Detalhe';

        $this->cod_abandono_tipo = $_GET['cod_abandono_tipo'];

        $registro = LegacyAbandonmentType::find($this->cod_abandono_tipo)?->getAttributes();

        if (!$registro) {
            $this->redirectNotFound(
                url: 'educar_abandono_tipo_lst.php',
                registro: 'motivo de deixou de frequentar',
                codigo: $this->cod_abandono_tipo,
            );
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes;
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nome']) {
            $this->addDetalhe(detalhe: ['Motivo - Deixou de Frequentar', "{$registro['nome']}"]);
        }
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 950, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_abandono_tipo_cad.php';
            $this->url_editar = "educar_abandono_tipo_cad.php?cod_abandono_tipo={$registro['cod_abandono_tipo']}";
        }
        $this->url_cancelar = 'educar_abandono_tipo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do tipo da situação deixou de frequentar', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo - Deixou de Frequentar';
        $this->processoAp = '950';
    }
};
