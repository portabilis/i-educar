<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $ref_cod_calendario_ano_letivo;
    public $mes;
    public $dia;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_calendario_dia_motivo;
    public $ref_cod_calendario_atividade;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Calendario Dia - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if (!$this->ref_cod_escola) {
            $this->ref_cod_escola = $obj_permissoes->getEscola(int_idpes_usuario: $this->pessoa_logada);
        }
        if (!$this->ref_cod_instituicao) {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);
        }

        $this->addCabecalhos(coluna: [
            'Calendario Ano Letivo',
            'Dia',
            'Mes',
            'Calendario Dia Motivo'
        ]);

        $get_escola     = 1;
        $obrigatorio    = true;
        include('include/pmieducar/educar_campo_lista.php');

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_calendario_dia = new clsPmieducarCalendarioDia();
        $obj_calendario_dia->setOrderby(strNomeCampo: 'descricao ASC');
        $obj_calendario_dia->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_calendario_dia->lista(
            int_ref_cod_calendario_ano_letivo: $this->ref_cod_calendario_ano_letivo,
            int_mes: $this->mes,
            int_dia: $this->dia,
            int_ref_cod_calendario_dia_motivo: $this->ref_cod_calendario_dia_motivo,
            str_descricao: $this->ref_cod_calendario_atividade,
            date_descricao_fim: $this->descricao_ini,
            date_data_cadastro_ini: $this->descricao_fim,
            date_data_exclusao_fim: 1,
            int_ativo: $this->ref_cod_escola
        );

        $total = $obj_calendario_dia->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo(cod_calendario_dia_motivo: $registro['ref_cod_calendario_dia_motivo']);
                $det_ref_cod_calendario_dia_motivo = $obj_ref_cod_calendario_dia_motivo->detalhe();
                $registro['ref_cod_calendario_dia_motivo'] = $det_ref_cod_calendario_dia_motivo['nm_motivo'];

                $obj_ref_cod_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $registro['ref_cod_calendario_ano_letivo']);
                $det_ref_cod_calendario_ano_letivo = $obj_ref_cod_calendario_ano_letivo->detalhe();
                $registro['ano'] = $det_ref_cod_calendario_ano_letivo['ano'];

                $this->addLinhas(linha: [
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro['ref_cod_calendario_ano_letivo']}&ano={$registro['ano']}&mes={$registro['mes']}&dia={$registro['dia']}\">{$registro['ano']}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro['ref_cod_calendario_ano_letivo']}&ano={$registro['ano']}&mes={$registro['mes']}&dia={$registro['dia']}\">{$registro['dia']}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro['ref_cod_calendario_ano_letivo']}&ano={$registro['ano']}&mes={$registro['mes']}&dia={$registro['dia']}\">{$registro['mes']}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro['ref_cod_calendario_ano_letivo']}&ano={$registro['ano']}&mes={$registro['mes']}&dia={$registro['dia']}\">{$registro['ref_cod_calendario_dia_motivo']}</a>"
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_calendario_dia_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 0, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0)) {
            $this->acao = 'go("educar_calendario_dia_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Calendario Dia';
        $this->processoAp = '621';
    }
};


