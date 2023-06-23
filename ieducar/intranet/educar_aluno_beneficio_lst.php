<?php

use App\Models\LegacyBenefit;

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

    public $cod_aluno_beneficio;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_beneficio;

    public $desc_beneficio;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Benefício Aluno - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Beneficio',
        ]);

        // outros Filtros
        $this->campoTexto(nome: 'nm_beneficio', campo: 'Benefício', valor: $this->nm_beneficio, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = LegacyBenefit::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_beneficio', direction: 'ASC');

        if (is_string(value: $this->nm_beneficio)) {
            $query->where(column: 'nm_beneficio', operator: 'ilike', value: '%' . $this->nm_beneficio . '%');
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas(linha: [
                    "<a href=\"educar_aluno_beneficio_det.php?cod_aluno_beneficio={$registro['cod_aluno_beneficio']}\">{$registro['nm_beneficio']}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_aluno_beneficio_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 581, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_aluno_beneficio_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Tipos de benefício do aluno', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Benefício do aluno';
        $this->processoAp = '581';
    }
};
