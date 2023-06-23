<?php

use App\Models\LegacyRace;

return new class extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $__pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $__offset;

    public $cod_raca;

    public $idpes_exc;

    public $idpes_cad;

    public $nm_raca;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = 'Raça - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Raça',
        ]);

        // outros Filtros
        $this->campoTexto(nome: 'nm_raca', campo: 'Raça', valor: $this->nm_raca, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $query = LegacyRace::query()
            ->where(column: 'ativo', operator: true)
            ->orderBy(column: 'nm_raca', direction: 'ASC');

        if (is_string(value: $this->nm_raca)) {
            $query->where(column: 'nm_raca', operator: 'ilike', value: '%' . $this->nm_raca . '%');
        }

        $result = $query->paginate(perPage: $this->__limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(datetime: substr(string: $registro['data_cadastro'], offset: 0, length: 16));
                $registro['data_cadastro_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(datetime: substr(string: $registro['data_exclusao'], offset: 0, length: 16));
                $registro['data_exclusao_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_exclusao_time']);

                $this->addLinhas(linha: [
                    "<a href=\"educar_raca_det.php?cod_raca={$registro['cod_raca']}\">{$registro['nm_raca']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_raca_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->__limite);

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 678, int_idpes_usuario: $this->__pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_raca_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de raças', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Raça';
        $this->processoAp = '678';
    }
};
