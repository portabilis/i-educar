<?php

return new class extends clsListagem {
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
            $this->$var = ($val === '') ? null: $val;
        }

        $this->ref_ref_cod_instituicao=($_GET['ref_cod_instituicao'] == '') ? $_GET['ref_ref_cod_instituicao'] : $_GET['ref_cod_instituicao'];

        $lista_busca = [
            'Avalia&ccedil;&atilde;o',
            'Servidor'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->campoTexto('titulo_avaliacao', 'Avalia&ccedil;&atilde;o', $this->titulo_avaliacao, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_avaliacao_desempenho = new clsPmieducarAvaliacaoDesempenho();
        $obj_avaliacao_desempenho->setOrderby('titulo_avaliacao ASC');
        $obj_avaliacao_desempenho->setLimite($this->limite, $this->offset);

        $lista = $obj_avaliacao_desempenho->lista(
            null,
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->titulo_avaliacao
        );

        $total = $obj_avaliacao_desempenho->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_servidor = new clsPessoa_($registro['ref_cod_servidor']);
                $det_cod_servidor = $obj_cod_servidor->detalhe();
                $nm_servidor = $det_cod_servidor['nome'];

                $obj_instituicao = new clsPmieducarInstituicao($registro['ref_ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();
                $nm_instituicao = $det_instituicao['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$registro['titulo_avaliacao']}</a>",
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$nm_servidor}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro['sequencial']}&ref_cod_servidor={$registro['ref_cod_servidor']}&ref_ref_cod_instituicao={$registro['ref_ref_cod_instituicao']}\">{$nm_instituicao}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}", $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            //$this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb('Registro da avaliação de desempenho do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Avaliação Desempenho';
        $this->processoAp = '635';
    }
};
