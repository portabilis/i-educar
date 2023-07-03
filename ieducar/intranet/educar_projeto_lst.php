<?php

use App\Models\LegacyProject;

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

    public $cod_projeto;

    public $nome;

    public $observacao;

    public function Gerar()
    {
        $this->titulo = 'Projetos - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Nome do projeto',
            'Observação',
        ]);

        $this->campoTexto(nome: 'nome', campo: 'Nome do projeto', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = LegacyProject::query()
            ->orderBy(column: 'nome', direction: 'ASC');

        if (is_string(value: $this->nome)) {
            $query->where(column: 'nome', operator: 'ilike', value: '%' . $this->nome . '%');
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_');

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas(linha: [
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}\">{$registro['observacao']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_projeto_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: null, intResultadosPorPagina: $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 21250, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_projeto_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de projetos', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
