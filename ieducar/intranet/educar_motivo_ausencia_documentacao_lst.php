<?php

use App\Models\DocumentAbsenceReason;
use App\Process;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $name;

    public function Gerar()
    {
        $this->titulo = 'Motivos de ausência de documentação - Listagem';

        $this->name = request('name');

        $this->addCabecalhos(coluna: [
            'Motivo',
            'Exige observação',
            'Situação',
        ]);

        $this->campoTexto(
            nome: 'name',
            campo: 'Motivo',
            valor: $this->name,
            tamanhovisivel: 30,
            tamanhomaximo: 191
        );

        $this->limite = 20;

        $result = DocumentAbsenceReason::withTrashed()
            ->when(is_string($this->name) && $this->name !== '', fn ($query) => $query->whereName($this->name))
            ->orderBy('name')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $result->items();
        $total = $result->total();

        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $link = 'educar_motivo_ausencia_documentacao_det.php?id=' . $registro->getKey();

                $this->addLinhas(linha: [
                    '<a href="' . $link . '">' . e($registro->name) . '</a>',
                    '<a href="' . $link . '">' . ($registro->requires_observation ? 'Sim' : 'Não') . '</a>',
                    '<a href="' . $link . '">' . ($registro->trashed() ? 'Inativo' : 'Ativo') . '</a>',
                ]);
            }
        }

        $this->addPaginador2(strUrl: 'educar_motivo_ausencia_documentacao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: Process::DOCUMENT_ABSENCE_REASON, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_motivo_ausencia_documentacao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Motivos de ausência de documentação', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivos de ausência de documentação';
        $this->processoAp = Process::DOCUMENT_ABSENCE_REASON;
    }
};
