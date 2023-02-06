<?php

return new class extends clsListagem {
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

    public $cod_modulo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $num_etapas;
    public $num_meses;
    public $num_semanas;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Etapa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Etapa',
            'Número de etapas',
            'Número de meses',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos(coluna: $lista_busca);

        // Filtros de Foreign Keys
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoTexto(nome: 'nm_tipo', campo: 'Etapa', valor: $this->nm_tipo, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoNumero(nome: 'num_meses', campo: 'Número de meses', valor: $this->num_meses, tamanhovisivel: 2, tamanhomaximo: 2);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_modulo = new clsPmieducarModulo();
        $obj_modulo->setOrderby(strNomeCampo: 'nm_tipo ASC');
        $obj_modulo->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_modulo->lista(
            str_nm_tipo: $this->nm_tipo,
            int_num_meses: $this->num_meses,
            int_ativo: 1,
            int_ref_cod_instituicao: $this->ref_cod_instituicao
        );

        $total = $obj_modulo->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['num_etapas']}</a>",
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['num_meses']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas(linha: $lista_busca);
            }
        }

        $this->addPaginador2(strUrl: 'educar_modulo_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 584, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_modulo_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de etapas', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Etapa';
        $this->processoAp = '584';
    }
};
