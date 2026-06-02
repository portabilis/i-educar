<?php

use App\Models\RegionalType;

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

    public $name;

    public $description;

    public function Gerar()
    {
        $this->titulo = 'Regionais - Listagem';

        $this->name = request('name');

        $this->addCabecalhos(coluna: [
            'Regional',
            'Descrição',
        ]);

        // outros Filtros
        $this->campoTexto(nome: 'name', campo: 'Regional', valor: $this->name, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = RegionalType::query()
            ->orderBy('name');

        if (is_string(value: $this->name)) {
            $query->where(column: 'name', operator: 'ilike', value: '%' . $this->name . '%');
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas(linha: [
                    "<a href=\"educar_regional_det.php?id={$registro['id']}\">{$registro['name']}</a>",
                    "<a href=\"educar_regional_det.php?id={$registro['id']}\">{$registro['description']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_regional_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $obj_permissao = new clsPermissoes;

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 5841, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_regional_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Regionais', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Regionais';
        $this->processoAp = '5841';
    }
};
