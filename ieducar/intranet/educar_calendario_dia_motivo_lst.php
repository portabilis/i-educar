<?php

use App\Models\LegacyCalendarDayReason;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_calendario_dia_motivo;

    public $ref_cod_escola;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $sigla;

    public $descricao;

    public $tipo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $nm_motivo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Calendário Dia Motivo - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Motivo',
            'Tipo',
            'Escola',
            'Instituição',
        ];

        $obj_permissao = new clsPermissoes();
        $obj_permissao->nivel_acesso($this->pessoa_logada);
        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola'], inputOptions: ['required' => false]);
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo', valor: $this->tipo, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = LegacyCalendarDayReason::query()
            ->orderBy('nm_motivo');

        if ($this->ref_cod_instituicao) {
            $query->whereHas('school', function ($query) {
                $query->where('ref_cod_instituicao', $this->ref_cod_instituicao);
            });
        }
        if ($this->ref_cod_escola) {
            $query->where('ref_cod_escola', $this->ref_cod_escola);
        }
        if ($this->nm_motivo) {
            $query->where('nm_motivo', 'ilike', "%{$this->nm_motivo}%");
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        foreach ($lista as $registro) {
            $lista_busca = [
                "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro->name}</a>",
                "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro->type}</a>",
                "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro->school_name}</a>",
                "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro->institution_name}</a>",
            ];
            $this->addLinhas($lista_busca);
        }

        $this->addPaginador2(strUrl: 'educar_calendario_dia_motivo_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_calendario_dia_motivo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Tipos de evento do calendário', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia Motivo';
        $this->processoAp = '576';
    }
};
