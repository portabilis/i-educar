<?php

use App\Models\DocumentAbsenceReason;
use App\Process;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $id;

    public $name;

    public $requires_observation;

    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = request('id');

        $this->ativo = 1;

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: Process::DOCUMENT_ABSENCE_REASON,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_motivo_ausencia_documentacao_lst.php'
        );

        if (is_numeric($this->id)) {
            $motivo = DocumentAbsenceReason::withTrashed()->find($this->id);

            if ($motivo) {
                $this->name = $motivo->name;
                $this->requires_observation = (int) $motivo->requires_observation;
                $this->ativo = (int) !$motivo->trashed();

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_motivo_ausencia_documentacao_det.php?id={$this->id}" : 'educar_motivo_ausencia_documentacao_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' motivo de ausência de documentação', breadcrumbs: [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'id', valor: $this->id);

        $this->campoTexto(
            nome: 'name',
            campo: 'Motivo',
            valor: $this->name,
            tamanhovisivel: 60,
            tamanhomaximo: 191,
            obrigatorio: true
        );

        $this->campoLista(
            nome: 'requires_observation',
            campo: 'Exige observação',
            valor: ['' => 'Selecione', 0 => 'Não', 1 => 'Sim'],
            default: $this->requires_observation
        );
        $this->campoLista(
            nome: 'ativo',
            campo: 'Situação',
            valor: [0 => 'Inativo', 1 => 'Ativo'],
            default: $this->ativo
        );
    }

    public function Novo()
    {
        DocumentAbsenceReason::create([
            'name' => $this->name,
            'requires_observation' => (bool) $this->requires_observation,
            'deleted_at' => (int) $this->ativo === 1 ? null : now(),
        ]);

        $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
        $this->simpleRedirect('educar_motivo_ausencia_documentacao_lst.php');
    }

    public function Editar()
    {
        $motivo = DocumentAbsenceReason::withTrashed()->findOrFail($this->id);

        $motivo->fill([
            'name' => $this->name,
            'requires_observation' => (bool) $this->requires_observation,
            'deleted_at' => (int) $this->ativo === 1 ? null : $motivo->deleted_at ?? now(),
        ]);

        $motivo->save();

        $this->mensagem = 'Edição efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_motivo_ausencia_documentacao_lst.php');
    }

    public function Formular()
    {
        $this->title = 'Motivos de ausência de documentação';
        $this->processoAp = Process::DOCUMENT_ABSENCE_REASON;
    }
};
