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

    public $cod_calendario_dia_motivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $sigla;
    public $descricao;
    public $tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_motivo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Calend&aacute;rio Dia Motivo - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Motivo',
            'Tipo',
            'Escola',
            'Instituição'
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        $get_escola = true;

        // outros Filtros
        $this->inputsHelper()->dynamic(['instituicao', 'escola'], ['required' => false]);
        $this->campoTexto('nm_motivo', 'Motivo', $this->tipo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_calendario_dia_motivo->codUsuario = $this->pessoa_logada;
        }

        $obj_calendario_dia_motivo->setOrderby('nm_motivo ASC');
        $obj_calendario_dia_motivo->setLimite($this->limite, $this->offset);

        $lista = $obj_calendario_dia_motivo->lista(
            null,
            $this->ref_cod_escola,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->nm_motivo,
            $this->ref_cod_instituicao
        );

        $total = $obj_calendario_dia_motivo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $obj_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $obj_cod_escola_det = $obj_cod_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_cod_escola_det['nome'];

                if ($registro['tipo'] == 'e') {
                    $registro['tipo'] = 'extra';
                } elseif ($registro['tipo'] == 'n') {
                    $registro['tipo'] = 'n&atilde;o-letivo';
                }
                $lista_busca = [
                    "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro['nm_motivo']}</a>",
                    "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro['tipo']}</a>",
                    "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro['ref_cod_escola']}</a>",
                    "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}\">{$registro['ref_cod_instituicao']}</a>"
                ];
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_calendario_dia_motivo_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissao->permissao_cadastra(576, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_calendario_dia_motivo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Tipos de evento do calendário', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Calend&aacute;rio Dia Motivo';
        $this->processoAp = '576';
    }
};
