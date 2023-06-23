<?php

use App\Models\PerformanceEvaluation;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $sequencial;

    public $ref_cod_servidor;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $titulo_avaliacao;

    public $ref_ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Avaliação Desempenho - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->ref_ref_cod_instituicao = ($_GET['ref_cod_instituicao'] == '') ? $_GET['ref_ref_cod_instituicao'] : $_GET['ref_cod_instituicao'];

        $lista_busca = [
            'Avaliação',
            'Servidor',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos(coluna: $lista_busca);

        // outros Filtros
        $this->campoTexto(nome: 'titulo_avaliacao', campo: 'Avaliação', valor: $this->titulo_avaliacao, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $avaliacoes = PerformanceEvaluation::query()
            ->where('employee_id', $this->ref_cod_servidor)
            ->with(['employee', 'institution'])
            ->orderBy('created_at', 'desc')
            ->limit($this->limite)
            ->offset($this->offset);

        $total = PerformanceEvaluation::query()
            ->where('employee_id', $this->ref_cod_servidor)
            ->count();

        $avaliacoes->each(function (PerformanceEvaluation $avaliacao) use ($nivel_usuario) {
            $lista_busca = [
                "<a href=\"educar_avaliacao_desempenho_det.php?id={$avaliacao->id}\">{$avaliacao->title}</a>",
                "<a href=\"educar_avaliacao_desempenho_det.php?id={$avaliacao->id}\">{$avaliacao->employee->person->name}</a>",
            ];

            if ($nivel_usuario == 1) {
                $lista_busca[] = $avaliacao->institution->name;
            }

            $this->addLinhas(linha: $lista_busca);
        });

        $this->addPaginador2(strUrl: "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}", intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Registro da avaliação de desempenho do servidor', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação Desempenho';
        $this->processoAp = '635';
    }
};
