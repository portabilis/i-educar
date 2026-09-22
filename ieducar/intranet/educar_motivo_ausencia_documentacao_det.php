<?php

use App\Models\DocumentAbsenceReason;
use App\Process;

return new class extends clsDetalhe
{
    public $titulo;

    public $id;

    public function Gerar()
    {
        $this->titulo = 'Motivo de ausência de documentação - Detalhe';

        $motivo = DocumentAbsenceReason::withTrashed()->find(request()->integer('id'));

        if (!$motivo) {
            $this->simpleRedirect(url: 'educar_motivo_ausencia_documentacao_lst.php');
        }

        $this->addDetalhe(detalhe: ['Motivo', e($motivo->name)]);
        $this->addDetalhe(detalhe: ['Exige observação', $motivo->requires_observation ? 'Sim' : 'Não']);
        $this->addDetalhe(detalhe: ['Situação', $motivo->trashed() ? 'Inativo' : 'Ativo']);

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: Process::DOCUMENT_ABSENCE_REASON, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_motivo_ausencia_documentacao_cad.php';
            $this->url_editar = "educar_motivo_ausencia_documentacao_cad.php?id={$motivo->getKey()}";
        }

        $this->url_cancelar = 'educar_motivo_ausencia_documentacao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do motivo de ausência de documentação', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivos de ausência de documentação';
        $this->processoAp = Process::DOCUMENT_ABSENCE_REASON;
    }
};
