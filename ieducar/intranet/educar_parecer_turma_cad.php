<?php

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_turma;

    public $parecer_1_etapa;

    public $parecer_2_etapa;

    public $parecer_3_etapa;

    public $parecer_4_etapa;

    public $etapas;

    public $ano;

    public function Inicializar()
    {
        $this->cod_turma = $_GET['cod_turma'];

        $obj = new clsPmieducarTurma(cod_turma: $this->cod_turma);
        $registro = $obj->detalhe();

        foreach ($registro as $campo => $val) {
            $this->$campo = $val;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_turma_lst.php');

        $this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

        $this->breadcrumb(currentPage: 'Pareceres da turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        $cursoId = $this->ref_cod_curso;

        $sql = 'select padrao_ano_escolar from pmieducar.curso where cod_curso = $1 and ativo = 1';
        $padraoAnoLetivo = Portabilis_Utils_Database::fetchPreparedQuery(sql: $sql, options: ['params' => $cursoId,
            'return_only' => 'first-field']);

        if ($padraoAnoLetivo == 1) {
            $escolaId = $this->ref_ref_cod_escola;
            $ano = $this->ano;

            $sql = 'select padrao.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.ano_letivo_modulo
              as padrao, pmieducar.modulo where padrao.ref_ano = $1 and padrao.ref_ref_cod_escola = $2
              and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial';

            $this->etapas = Portabilis_Utils_Database::fetchPreparedQuery(sql: $sql, options: ['params' => [$ano, $escolaId]]);
        } else {
            $sql = 'select turma.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.turma_modulo as turma,
              pmieducar.modulo where turma.ref_cod_turma = $1 and turma.ref_cod_modulo = modulo.cod_modulo
              and modulo.ativo = 1 order by turma.sequencial';

            $this->etapas = Portabilis_Utils_Database::fetchPreparedQuery(sql: $sql, options: ['params' => $this->cod_turma]);
        }

        return 'Editar';
    }

    public function Gerar()
    {

        $this->campoOculto(nome: 'cod_turma', valor: $this->cod_turma);
        $this->campoOculto(nome: 'ano', valor: $this->ano);

        $this->campoMemo(nome: 'parecer_1_etapa', campo: 'Relatório global da turma - 1° Semestre', valor: $this->parecer_1_etapa, colunas: 60, linhas: 5, obrigatorio: false);
        $this->campoMemo(nome: 'parecer_2_etapa', campo: 'Relatório global de educação física - 1° Semestre', valor: $this->parecer_2_etapa, colunas: 60, linhas: 5, obrigatorio: false);
        $this->campoMemo(nome: 'parecer_3_etapa', campo: 'Relatório global da turma - 2° Semestre', valor: $this->parecer_3_etapa, colunas: 60, linhas: 5, obrigatorio: false);
        $this->campoMemo(nome: 'parecer_4_etapa', campo: 'Relatório global de educação física - 2° Semestre', valor: $this->parecer_4_etapa, colunas: 60, linhas: 5, obrigatorio: false);
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_turma_lst.php');

        $obj = new clsPmieducarTurma(cod_turma: $this->cod_turma);
        $obj->ref_usuario_exc = $this->pessoa_logada;
        $obj->parecer_1_etapa = $this->parecer_1_etapa;
        $obj->parecer_2_etapa = $this->parecer_2_etapa;
        $obj->parecer_3_etapa = $this->parecer_3_etapa;
        $obj->parecer_4_etapa = $this->parecer_4_etapa;
        $obj->ano = $this->ano;

        if ($obj->edita()) {
            $this->simpleRedirect(url: "educar_turma_det.php?cod_turma={$this->cod_turma}");
        }
        $this->mensagem = 'Erro ao salvar lançamentos.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Parecer da turma';
        $this->processoAp = '586';
    }
};
