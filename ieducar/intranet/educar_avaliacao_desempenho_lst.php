<?php

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

        $obj_avaliacao_desempenho = new clsPmieducarAvaliacaoDesempenho();
        $obj_avaliacao_desempenho->setOrderby(strNomeCampo: 'titulo_avaliacao ASC');
        $obj_avaliacao_desempenho->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_avaliacao_desempenho->lista(
            int_ref_cod_servidor: $this->ref_cod_servidor,
            int_ref_ref_cod_instituicao: $this->ref_ref_cod_instituicao,
            int_ativo: 1,
            str_titulo_avaliacao: $this->titulo_avaliacao
        );

        $total = $obj_avaliacao_desempenho->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_cod_servidor = new clsPessoa_(int_idpes: $registro['ref_cod_servidor']);
                $det_cod_servidor = $obj_cod_servidor->detalhe();
                $nm_servidor = $det_cod_servidor['nome'];

                $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();
                $nm_instituicao = $det_instituicao['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$registro['titulo_avaliacao']}</a>",
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$nm_servidor}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$nm_instituicao}</a>";
                }
                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}", intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            //$this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
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
