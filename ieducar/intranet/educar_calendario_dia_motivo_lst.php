<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
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
        $this->titulo = 'Calendário Dia Motivo - Listagem';

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
        $obj_permissao->nivel_acesso($this->pessoa_logada);
        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola'], inputOptions: ['required' => false]);
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo', valor: $this->tipo, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_calendario_dia_motivo->codUsuario = $this->pessoa_logada;
        }

        $obj_calendario_dia_motivo->setOrderby('nm_motivo ASC');
        $obj_calendario_dia_motivo->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_calendario_dia_motivo->lista(
            int_ref_cod_escola: $this->ref_cod_escola,
            int_ativo: 1,
            str_nm_motivo: $this->nm_motivo,
            int_ref_cod_instituicao: $this->ref_cod_instituicao
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
                    $registro['tipo'] = 'não-letivo';
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
        $this->addPaginador2(strUrl: 'educar_calendario_dia_motivo_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 576, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_calendario_dia_motivo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Tipos de evento do calendário', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia Motivo';
        $this->processoAp = '576';
    }
};
