<?php

use App\Models\SchoolNotice;

return new class extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public string $titulo_busca;

    public function Gerar()
    {
        $this->titulo = 'Comunicados Escolares - Listagem';

        $this->titulo_busca = request()->string('titulo_busca');

        $lista_busca = [
            'Título',
            'Data',
        ];

        $obj_permissao = new clsPermissoes;
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos(coluna: $lista_busca);
        $get_escola = true;

        // Filtros de Foreign Keys
        include_once 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

        // outros Filtros
        $this->campoTexto(nome: 'titulo_busca', campo: 'Título', valor: $this->titulo_busca, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $usuarioEscolar = null;
        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $usuarioEscolar = $this->pessoa_logada;
        }

        $query = SchoolNotice::query()
            ->with(['institution'])
            ->when($usuarioEscolar, function ($query, $usuarioEscolar) {
                $query->whereHas('schools.schoolUsers', function ($q) use ($usuarioEscolar) {
                    $q->where('ref_cod_usuario', $usuarioEscolar);
                });
            })
            ->orderByDesc('created_at');

        if (is_string(value: $this->titulo_busca)) {
            $query->where(column: 'title', operator: 'ilike', value: '%' . $this->titulo_busca . '%');
        }

        if (is_numeric(value: $this->ref_cod_instituicao)) {
            $query->where(column: 'institution_id', operator: $this->ref_cod_instituicao);
        }

        if (is_numeric(value: $this->ref_cod_escola)) {
            $query->whereHas('schools', fn ($q) => $q->whereKey($this->ref_cod_escola));
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_');

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $lista_busca = [
                    "<a href=\"educar_comunicados_escolares_det.php?id={$registro->getKey()}\">{$registro->title}</a>",
                    "<a href=\"educar_comunicados_escolares_det.php?id={$registro->getKey()}\">{$registro->date->format('d/m/Y')}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_comunicados_escolares_det.php?id={$registro->getKey()}\">{$registro->institution->nm_instituicao}</a>";
                }
                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_comunicados_escolares_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: null, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(SchoolNotice::PROCESS, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_comunicados_escolares_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de Comunicados Escolares', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Comunicados Escolares';
        $this->processoAp = SchoolNotice::PROCESS;
    }
};
