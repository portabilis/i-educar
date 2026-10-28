<?php

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyPerson;
use App\Services\IndividualCpfStateService;

return new class extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Pessoas Físicas';

        $par_nome = str_replace(['[', ']', '{', '}', '(', ')', '\\', '/'], '', $this->getQueryString(name: 'nm_pessoa')) ?: false;
        $par_id_federal = idFederal2Int(str: $this->getQueryString(name: 'id_federal')) ?: false;
        $par_situacao_cpf = (string) $this->getQueryString(name: 'situacao_cpf');
        $par_motivo_ausencia_cpf = (new IndividualCpfStateService)->reasonId($this->getQueryString(name: 'motivo_ausencia_cpf'));

        $situacoesCpf = [
            '' => 'Todos',
            'with_cpf' => 'Com CPF',
            'justified_absence' => 'Sem CPF - ausência justificada',
            'unjustified_absence' => 'Sem CPF - sem justificativa',
        ];

        if (!isset($situacoesCpf[$par_situacao_cpf])) {
            $par_situacao_cpf = '';
        }

        $this->addCabecalhos(
            coluna: [
                'Nome',
                'CPF',
            ]
        );
        $this->campoTexto(
            nome: 'nm_pessoa',
            campo: 'Nome',
            valor: $par_nome,
            tamanhovisivel: '50',
            tamanhomaximo: '255'
        );

        $this->campoCpf(
            nome: 'id_federal',
            campo: 'CPF',
            valor: $par_id_federal
        );

        $this->campoLista(
            nome: 'situacao_cpf',
            campo: 'Situação do CPF',
            valor: $situacoesCpf,
            default: $par_situacao_cpf,
            obrigatorio: false
        );

        $this->campoLista(
            nome: 'motivo_ausencia_cpf',
            campo: 'Motivos da ausência de CPF',
            valor: DocumentAbsenceReason::query()
                ->where('from_system', false)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->map(fn ($name) => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'))
                ->prepend('Todos', '')
                ->toArray(),
            default: $par_motivo_ausencia_cpf,
            obrigatorio: false
        );

        // Paginador
        $limite = 10;

        $lista = LegacyPerson::query()->filter([
            'name' => $par_nome,
            'cpf' => is_numeric($par_id_federal) ? $par_id_federal : null,
            'cpf_situation' => $par_situacao_cpf,
            'absence_reason' => $par_situacao_cpf === 'justified_absence' ? $par_motivo_ausencia_cpf : null,
        ])->select([
            'idpes',
            'nome',
        ])->with([
            'individual:idpes,cpf',
        ])->active()->orderBy('nome')->paginate(
            perPage: $limite,
            pageName: "pagina_{$this->nome}",
        );

        $total = $lista->total();

        $this->adicionarPessoas($lista);

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(
            int_processo_ap: 43,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            super_usuario: true
        )) {
            $this->acao = 'go("atendidos_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->addPaginador2(
            strUrl: 'atendidos_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $limite
        );

        Portabilis_View_Helper_Application::embedStylesheet(
            viewInstance: $this,
            css: '.cpf-situacao { display: inline-block; padding: 3px 9px; border-radius: 10px; font-weight: 600; white-space: nowrap; }
                .cpf-situacao-com { background: #e6f4e6; color: #2e7d32; }
                .cpf-situacao-sem { background: #fcefef; color: #c05e5e; }'
        );

        Portabilis_View_Helper_Application::embedJavascript(
            viewInstance: $this,
            script: "var checkSituacaoCpf = function () { $('#tr_motivo_ausencia_cpf').toggle($('#situacao_cpf').val() === 'justified_absence'); }; checkSituacaoCpf(); $('#situacao_cpf').on('change', checkSituacaoCpf);",
            afterReady: true
        );

        $this->breadcrumb(
            currentPage: 'Listagem de pessoa física',
            breadcrumbs: ['educar_pessoas_index.php' => 'Pessoas']
        );
    }

    private function adicionarPessoas(iterable $lista): void
    {
        foreach ($lista as $pessoa) {
            $cod = $pessoa->getKey();
            $nome = $pessoa->name;

            if ($pessoa->social_name) {
                // Inalcançável: a pessoa física é carregada só com o CPF, então o nome social nunca vem preenchido.
                // @codeCoverageIgnoreStart
                $nome = $pessoa->social_name . '<br> <i>Nome de registro: </i>' . $pessoa->name;
                // @codeCoverageIgnoreEnd
            }

            $cpf = $pessoa->individual?->cpf
                ? "<span class='cpf-situacao cpf-situacao-com'>{$pessoa->individual->cpf}</span>"
                : "<span class='cpf-situacao cpf-situacao-sem'>Sem CPF</span>";
            $this->addLinhas(linha: [
                "<img src='imagens/noticia.jpg' border=0><a href='atendidos_det.php?cod_pessoa={$cod}'>" . e($nome) . '</a>',
                $cpf,
            ]);
        }
    }

    public function Formular()
    {
        $this->title = 'Pessoas Físicas';
        $this->processoAp = '43';
    }
};
