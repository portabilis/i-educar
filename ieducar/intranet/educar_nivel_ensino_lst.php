<?php

use App\Models\LegacyEducationLevel;

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

    public $cod_nivel_ensino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_nivel;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Nível Ensino - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Nivel Ensino',
        ]);

        $lista_busca = [
            'Nível Ensino',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }
        $this->addCabecalhos(coluna: $lista_busca);

        // Filtros de Foreign Keys
        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto(nome: 'nm_nivel', campo: 'Nível Ensino', valor: $this->nm_nivel, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = LegacyEducationLevel::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_nivel', direction: 'ASC');

        if (is_string(value: $this->nm_nivel)) {
            $query->where(column: 'nm_nivel', operator: 'ilike', value: '%' . $this->nm_nivel . '%');
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_nivel_ensino_det.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}\">{$registro['nm_nivel']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_nivel_ensino_det.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_nivel_ensino_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_nivel_ensino_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de níveis de ensino', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Nível Ensino';
        $this->processoAp = '571';
    }
};
