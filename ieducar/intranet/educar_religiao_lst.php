<?php

use App\Models\Religion;

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

    public $cod_religiao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_religiao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Religiao - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Nome Religião',
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto(nome: 'nm_religiao', campo: 'Nome Religião', valor: $this->nm_religiao, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = Religion::query()->orderBy(column: 'name');

        if (is_string(value: $this->nm_religiao)) {
            $query->where(column: 'name', operator: 'ilike', value: '%' . $this->nm_religiao . '%');
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas(linha: [
                    "<a href=\"educar_religiao_det.php?cod_religiao={$registro['id']}\">{$registro['name']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_religiao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 579, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_religiao_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de religiões', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Religiao';
        $this->processoAp = '579';
    }
};
