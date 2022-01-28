<?php

use iEducar\Legacy\Model;

class clsPmieducarMatriculaTurma extends Model
{
    public $ref_cod_matricula;
    public $ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_turma_transf;
    public $sequencial;
    public $data_enturmacao;
    public $sequencial_fechamento;
    public $removerSequencial;
    public $reabrirMatricula;
    public $etapa_educacenso;
    public $turma_unificada;
    public $remanejado;
    public $turno_id;
    public $tipo_atendimento = false;

    public function __construct(
        $ref_cod_matricula = null,
        $ref_cod_turma = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ref_cod_turma_transf = null,
        $sequencial = null,
        $data_enturmacao = null,
        $removerSequencial = false,
        $reabrirMatricula = false,
        $remanejado = false
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}matricula_turma";

        $this->_campos_lista = $this->_todos_campos = 'mt.ref_cod_matricula, mt.abandono, mt.reclassificado, mt.remanejado, mt.transferido, mt.falecido, mt.ref_cod_turma, mt.etapa_educacenso, mt.turma_unificada, mt.ref_usuario_exc, mt.ref_usuario_cad, mt.data_cadastro, mt.data_exclusao, mt.ativo, mt.sequencial, mt.data_enturmacao, mt.turno_id, mt.tipo_atendimento, (SELECT pes.nome FROM cadastro.pessoa pes, pmieducar.aluno alu, pmieducar.matricula mat WHERE pes.idpes = alu.ref_idpes AND mat.ref_cod_aluno = alu.cod_aluno AND mat.cod_matricula = mt.ref_cod_matricula ) AS nome, (SELECT (pes.nome) FROM cadastro.pessoa pes, pmieducar.aluno alu, pmieducar.matricula mat WHERE pes.idpes = alu.ref_idpes AND mat.ref_cod_aluno = alu.cod_aluno AND mat.cod_matricula = mt.ref_cod_matricula ) AS nome_ascii';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($ref_cod_turma)) {
            $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (!empty($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (!empty($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if ($remanejado) {
            $this->remanejado = $remanejado;
        }

        if (is_numeric($ref_cod_turma_transf)) {
            $this->ref_cod_turma_transf = $ref_cod_turma_transf;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }

        if (is_string($data_enturmacao)) {
            $this->data_enturmacao = $data_enturmacao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
            is_numeric($this->ref_usuario_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_turma)) {
                $campos .= "{$gruda}ref_cod_turma";
                $valores .= "{$gruda}'{$this->ref_cod_turma}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa_educacenso)) {
                $campos .= "{$gruda}etapa_educacenso";
                $valores .= "{$gruda}{$this->etapa_educacenso}";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_unificada)) {
                $campos .= "{$gruda}turma_unificada";
                $valores .= "{$gruda}{$this->turma_unificada}";
                $gruda = ', ';
            }

            $this->sequencial = $this->buscaSequencialMax();

            $campos .= "{$gruda}sequencial";
            $valores .= "{$gruda}'{$this->sequencial}'";
            $gruda = ', ';

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            if (is_string($this->data_enturmacao)) {
                $campos .= "{$gruda}data_enturmacao";
                $valores .= "{$gruda}'{$this->data_enturmacao}'";
                $gruda = ', ';
            }

            $sequencialEnturmacao = new SequencialEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma, $this->data_enturmacao);
            $this->sequencial_fechamento = $sequencialEnturmacao->ordenaSequencialNovaMatricula();

            if (is_numeric($this->sequencial_fechamento)) {
                $campos .= "{$gruda}sequencial_fechamento";
                $valores .= "{$gruda}'{$this->sequencial_fechamento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_id)) {
                $campos .= "{$gruda}turno_id";
                $valores .= "{$gruda}'{$this->turno_id}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            $detalhe = $this->detalhe();

            return true;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
            is_numeric($this->ref_usuario_exc) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $set = '';

            $gruda = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_string($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
                $gruda = ', ';
            } elseif (is_null($this->data_exclusao) || empty($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = NULL";
                $gruda = ', ';
            }

            if ($this->etapa_educacenso === 0) {
                $set .= "{$gruda}etapa_educacenso = NULL";
                $gruda = ', ';
            } elseif (is_numeric($this->etapa_educacenso)) {
                $set .= "{$gruda}etapa_educacenso = {$this->etapa_educacenso}";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_unificada)) {
                $set .= "{$gruda}turma_unificada = {$this->turma_unificada}";
                $gruda = ', ';
            }

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
                if ($this->ativo == 1) {
                    $set .= "{$gruda}remanejado = null, transferido = null";
                    $gruda = ', ';
                }
            }

            if (!$this->ativo) {
                if ($this->remanejado) {
                    $set .= "{$gruda}remanejado = true";
                    $gruda = ', ';
                }
            }

            if (is_numeric($this->ref_cod_turma_transf)) {
                $set .= "{$gruda}ref_cod_turma= '{$this->ref_cod_turma_transf}'";
                $gruda = ', ';
            }

            if (is_string($this->data_enturmacao)) {
                $set .= "{$gruda}data_enturmacao = '{$this->data_enturmacao}'";
                $gruda = ', ';
            }

            if ($this->reabrirMatricula) {
                $det = $this->detalhe();
                $this->ref_usuario_cad = $det['ref_usuario_cad'];

                return $this->cadastra();
            }

            if ($this->removerSequencial) {
                $sequencialEnturmacao = new SequencialEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma, $this->data_enturmacao);
                $this->sequencial_fechamento = $sequencialEnturmacao->ordenaSequencialExcluiMatricula();
            }

            // FIXME
            // Este trecho de código não é utilizado na atualização do registro, ou
            // seja, não serve para nada. Verificar o impacto ao corrigi-lo.

            if (is_numeric($this->sequencial_fechamento)) {
                $campos .= "{$gruda}sequencial_fechamento";
                $valores .= "{$gruda}'{$this->sequencial_fechamento}'";
                $gruda = ', ';
            }

            if (is_string($this->turno_id) && $this->turno_id == 0) {
                $set .= "{$gruda}turno_id = NULL";
                $gruda = ', ';
            } elseif (is_string($this->turno_id) && !empty($this->turno_id)) {
                $set .= "{$gruda}turno_id = '{$this->turno_id}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo_atendimento)) {
                $set .= "{$gruda}tipo_atendimento = '{{$this->tipo_atendimento}}'";
                $gruda = ', ';
            } elseif ($this->tipo_atendimento !== false) {
                $set .= "{$gruda}tipo_atendimento = NULL";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' and sequencial = '$this->sequencial' ");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $int_ref_cod_matricula = null,
        $int_ref_cod_turma = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_instituicao = null,
        $int_ref_cod_aluno = null,
        $mes = null,
        $aprovado = null,
        $mes_menor_que = null,
        $int_sequencial = null,
        $int_ano_matricula = null,
        $tem_avaliacao = null,
        $bool_get_nome_aluno = false,
        $bool_aprovados_reprovados = null,
        $int_ultima_matricula = null,
        $bool_matricula_ativo = null,
        $bool_escola_andamento = false,
        $mes_matricula_inicial = false,
        $get_serie_mult = false,
        $int_ref_cod_serie_mult = null,
        $int_semestre = null,
        $pegar_ano_em_andamento = false,
        $parar = null,
        $diario = false,
        $int_turma_turno_id = false,
        $int_ano_turma = false,
        $dependencia = null,
        $apenasTurmasMultiSeriadas = false,
        $apenasTurmasUnificadas = false,
        $ultimoSequencialNaTurma = false
    ) {
        $nome = '';
        $tab_aluno = '';
        $where_nm_aluno = '';

        if ($bool_get_nome_aluno === true) {
            $nome = ' ,(SELECT (nome)
                        FROM cadastro.pessoa
                           WHERE idpes = a.ref_idpes
                    ) as nome_aluno';
            $tab_aluno = ", {$this->_schema}aluno a";

            $where_nm_aluno = ' AND a.cod_aluno = m.ref_cod_aluno';
        }

        $from = '';
        $where = '';

        if ($bool_escola_andamento) {
            if ($pegar_ano_em_andamento) {
                $from = ', pmieducar.escola_ano_letivo eal ';

                $where = ' AND eal.ref_cod_escola = m.ref_ref_cod_escola
              AND eal.ano = (select max(ano) from pmieducar.escola_ano_letivo let where
                      let.ref_cod_escola= eal.ref_cod_escola and andamento=1)
              AND eal.ano = m.ano
              AND eal.andamento = \'1\' ';
            } else {
                $ano = date('Y');

                $from = ', pmieducar.escola_ano_letivo eal ';

                $where = " AND eal.ref_cod_escola = m.ref_ref_cod_escola
              AND eal.ano = '{$ano}'
              AND eal.ano = m.ano
              AND eal.andamento = '1' ";
            }
        }

        $sql = "SELECT {$this->_campos_lista}, mt.sequencial_fechamento, c.nm_curso, t.nm_turma, i.nm_instituicao, m.ref_ref_cod_serie, m.ref_cod_curso, m.ref_ref_cod_escola, c.ref_cod_instituicao, m.ref_cod_aluno,t.hora_inicial, mt.turma_unificada, t.etapa_educacenso as etapa_ensino $nome FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t,{$this->_schema}aluno al, {$this->_schema}instituicao i{$tab_aluno} {$from}, cadastro.pessoa ";

        $whereAnd = ' AND ';
        $filtros = " WHERE mt.ref_cod_matricula = m.cod_matricula AND idpes = al.ref_idpes AND al.cod_aluno = m.ref_cod_aluno AND al.ativo=1 AND m.ref_cod_curso = c.cod_curso AND t.cod_turma = mt.ref_cod_turma AND i.cod_instituicao = c.ref_cod_instituicao {$where_nm_aluno} {$where}";

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ativo)) {
            if ($int_ativo == 1) {
                $filtros .= "{$whereAnd} mt.ativo = '1'";
                $whereAnd = ' AND ';
            } elseif ($int_ativo == 2) {
                $filtros .= "{$whereAnd}
                    (
                        mt.ativo = 1
                        OR
                        (
                            i.data_base_remanejamento IS NOT NULL
                            AND mt.data_exclusao::date > i.data_base_remanejamento
                            AND (
                                mt.transferido
                                OR mt.remanejado
                                OR mt.reclassificado
                                OR mt.abandono
                                OR mt.falecido
                            )

                        )
                    )
                ";
                $whereAnd = ' AND ';
            } elseif ($int_ativo == 3) {
                $filtros .= "{$whereAnd}
                    (
                        mt.ativo = 1
                        OR
                        (
                            (
                                mt.transferido
                                OR mt.remanejado
                                OR mt.reclassificado
                                OR mt.abandono
                                OR mt.falecido
                            )

                        )
                    )
                ";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} mt.ativo = '0'";
                $whereAnd = ' AND ';
            }
        }

        if (!is_null($bool_matricula_ativo) && is_bool($bool_matricula_ativo)) {
            if ($bool_matricula_ativo) {
                $filtros .= "{$whereAnd} m.ativo = '1'";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} m.ativo = '0'";
                $whereAnd = ' AND ';
            }
        }

        if (is_numeric($int_ref_cod_serie)) {
            if (!is_numeric($int_ref_cod_serie_mult)) {
                $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} (m.ref_ref_cod_serie = '{$int_ref_cod_serie}' OR ref_ref_cod_serie_mult='{$int_ref_cod_serie_mult}')";
                $whereAnd = ' AND ';
            }
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ultima_matricula)) {
            $filtros .= "{$whereAnd} m.ultima_matricula = '{$int_ultima_matricula}'";
            $whereAnd = ' AND ';
        }

        if ($apenasTurmasMultiSeriadas === true) {
            $etapas = implode(',', App_Model_Educacenso::etapas_multisseriadas());
            $filtros .= "{$whereAnd} t.etapa_educacenso IN ({$etapas}) ";
            $whereAnd = ' AND ';
        }

        if ($apenasTurmasUnificadas === true) {
            $etapas = implode(',', App_Model_Educacenso::etapasEnsinoUnificadas());
            $filtros .= "{$whereAnd} t.etapa_educacenso IN ({$etapas}) ";
            $whereAnd = ' AND ';
        }

        if (is_array($aprovado)) {
            $filtros .= "{$whereAnd} ( ";
            $whereAnd = '';

            foreach ($aprovado as $value) {
                $filtros .= "{$whereAnd} m.aprovado = '{$value}'";
                $whereAnd = ' OR ';
            }

            $filtros .= ' )';
            $whereAnd = ' AND ';
        } elseif (is_numeric($aprovado)) {
            $filtros .= "{$whereAnd} m.aprovado = '{$aprovado}' ";
            $whereAnd = ' AND ';
        }

        if (is_bool($bool_aprovados_reprovados)) {
            if ($bool_aprovados_reprovados == true) {
                $filtros .= "{$whereAnd} ( m.aprovado = '1'";
                $whereAnd = ' OR ';
                $filtros .= "{$whereAnd} m.aprovado = '2' )";
                $whereAnd = ' AND ';
            }
        }

        if ($int_ano_matricula) {
            $int_ano_matricula = (int) $int_ano_matricula;
            $filtros .= "{$whereAnd} m.ano = '{$int_ano_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_semestre)) {
            $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
            $whereAnd = ' AND ';
        }

        if ($mes) {
            $mes = (int) $mes;
            $filtros .= "{$whereAnd} ( to_char(mt.data_cadastro,'MM')::int = '$mes'
                      OR to_char(mt.data_exclusao,'MM')::int = '$mes' )";
            $whereAnd = ' AND ';
        }

        if ($mes_menor_que) {
            $mes_menor_que = (int) $mes_menor_que;
            $filtros .= "{$whereAnd} ( ( to_char(mt.data_cadastro,'MM')::int < '$mes_menor_que' AND mt.data_exclusao  IS NULL )
                    OR ( to_char(mt.data_exclusao,'MM')::int < '$mes_menor_que'  AND mt.data_exclusao  IS NOT NULL ) )";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_turma_turno_id)) {
            $filtros .= "{$whereAnd} t.turma_turno_id = '{$int_turma_turno_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ano_turma)) {
            $filtros .= "{$whereAnd} t.ano = '{$int_ano_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($mes_matricula_inicial)) {
            $filtros .= "AND ((TO_CHAR(mt.data_cadastro,'MM')::int < '{$mes_matricula_inicial}'
                  AND NOT EXISTS (SELECT 1
                                FROM pmieducar.transferencia_solicitacao ts
                             WHERE ref_cod_matricula_saida = m.cod_matricula
                               AND to_char(ts.data_cadastro,'MM')::int < '{$mes_matricula_inicial}' )
                    )
                    OR (TO_CHAR(mt.data_cadastro,'MM')::int < '{$mes_matricula_inicial}'
                         AND EXISTS (SELECT 1
                           FROM pmieducar.transferencia_solicitacao
                          WHERE ref_cod_matricula_saida = m.cod_matricula
                            AND (TO_CHAR(data_transferencia,'MM')::int = '{$mes_matricula_inicial}' OR m.aprovado = 3))
                    )
              )
              and not(TO_CHAR(mt.data_exclusao,'MM')::int < '$mes_matricula_inicial' and mt.ativo = 0)
              ";
        }

        if ($diario) {
            $filtros .= "{$whereAnd} (m.aprovado <> 6 OR mt.abandono)";
            $whereAnd = ' AND ';
        }
        if (is_string($dependencia)) {
            $filtros .= "{$whereAnd} m.dependencia = '{$dependencia}'";
            $whereAnd = ' AND ';
        }

        if ($ultimoSequencialNaTurma) {
            $filtros .= "{$whereAnd} mt.sequencial = (
                SELECT max(sequencial)
                FROM pmieducar.matricula_turma
                WHERE matricula_turma.ref_cod_turma = mt.ref_cod_turma
                AND matricula_turma.ref_cod_matricula = m.cod_matricula
            )";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        if ($parar) {
            die($sql);
        }

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} mt, cadastro.pessoa , {$this->_schema}matricula m, {$this->_schema}aluno al, {$this->_schema}curso c, {$this->_schema}turma t, {$this->_schema}instituicao i{$tab_aluno} {$from} {$filtros} {$where}");
        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function lista2(
        $int_ref_cod_matricula = null,
        $int_ref_cod_turma = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_instituicao = null,
        $int_ref_cod_aluno = null,
        $em_andamento = true,
        $mes = null,
        $aprovado = null,
        $mes_menor_que = null,
        $int_sequencial = null,
        $int_ano_matricula = null
    ) {
        $sql = "SELECT {$this->_campos_lista}, m.ref_ref_cod_serie, m.ref_cod_curso, m.ref_ref_cod_escola, c.ref_cod_instituicao, i.nm_instituicao, m.ref_cod_aluno,t.nm_turma,s.nm_serie,c.nm_curso FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}instituicao i";
        $filtros = '';

        $whereAnd = ' WHERE mt.ref_cod_matricula = m.cod_matricula AND m.ref_cod_curso = c.cod_curso AND mt.ref_cod_turma = t.cod_turma AND c.ref_cod_instituicao = i.cod_instituicao AND';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} mt.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} mt.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }

        if (!is_numeric($aprovado)) {
            if ($em_andamento == true) {
                $filtros .= "{$whereAnd} (m.aprovado = '3'";
                $whereAnd = ' OR ';
                $filtros .= "{$whereAnd} m.aprovado = '7')";
                $whereAnd = ' AND ';
            }
        }

        if ($int_ano_matricula) {
            $int_ano_matricula = (int) $int_ano_matricula;
            $filtros .= "{$whereAnd} (to_char(m.ano,'YYYY')::int = '$int_ano_matricula')";
            $whereAnd = ' AND ';
        }

        if ($mes) {
            $mes = (int) $mes;
            $filtros .= "{$whereAnd} (to_char(mt.data_cadastro,'MM')::int = '$mes'
                      OR to_char(mt.data_exclusao,'MM')::int = '$mes')";
            $whereAnd = ' AND ';
        }

        if ($mes_menor_que) {
            $mes_menor_que = (int) $mes_menor_que;
            $filtros .= "{$whereAnd} (to_char(mt.data_cadastro,'MM')::int < '$mes_menor_que'
                      OR to_char(mt.data_exclusao,'MM')::int < '$mes_menor_que')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($aprovado)) {
            $filtros .= "{$whereAnd} m.aprovado = '$aprovado'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}instituicao i {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function lista3(
        $int_ref_cod_matricula = null,
        $int_ref_cod_turma = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_cod_serie = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_escola = null,
        $int_ref_cod_aluno = null,
        $aprovado = null,
        $int_sequencial = null,
        $int_ano_matricula = null,
        $int_ultima_matricula = null,
        $int_matricula_ativo = null,
        $int_semestre = null
    ) {
        $sql = "SELECT {$this->_campos_lista}, c.nm_curso, s.nm_serie, t.nm_turma, c.ref_cod_instituicao, m.ref_ref_cod_escola, m.ref_cod_curso, m.ref_ref_cod_serie, m.ref_cod_aluno, p.nome,a.tipo_responsavel,f.data_nasc FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}serie s, {$this->_schema}turma t, {$this->_schema}aluno a, cadastro.pessoa p, cadastro.fisica f {$join}";
        $filtros = '';

        $whereAnd = ' WHERE mt.ref_cod_matricula = m.cod_matricula AND m.ref_cod_curso = c.cod_curso AND t.cod_turma = mt.ref_cod_turma AND s.cod_serie = m.ref_ref_cod_serie AND a.cod_aluno = m.ref_cod_aluno AND p.idpes = a.ref_idpes AND p.idpes = f.idpes AND';

        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} mt.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} mt.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ultima_matricula)) {
            $filtros .= "{$whereAnd} m.ultima_matricula = '{$int_ultima_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_array($aprovado)) {
            $filtros .= "{$whereAnd} ( ";
            $whereAnd = '';

            foreach ($aprovado as $value) {
                $filtros .= "{$whereAnd} m.aprovado = '{$value}'";
                $whereAnd = ' OR ';
            }

            $filtros .= ' )';
            $whereAnd = ' AND ';
        } elseif (is_numeric($aprovado)) {
            $filtros .= "{$whereAnd} m.aprovado = '{$aprovado}' ";
            $whereAnd = ' AND ';
        }

        if ($int_ano_matricula) {
            $int_ano_matricula = (int) $int_ano_matricula;
            $filtros .= "{$whereAnd} m.ano = '{$int_ano_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }

        if (!is_null($int_matricula_ativo) && is_bool($int_matricula_ativo)) {
            if ($int_matricula_ativo) {
                $filtros .= "{$whereAnd} m.ativo = '1'";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} m.ativo = '0'";
                $whereAnd = ' AND ';
            }
        }

        if (is_numeric($int_semestre)) {
            $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}serie s, {$this->_schema}turma t, {$this->_schema}aluno a, cadastro.pessoa p, cadastro.fisica f {$join} {$filtros}");
        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function lista4($escolaId = null, $cursoId = null, $serieId = null, $turmaId = null, $ano = null, $saida_escola = false)
    {
        $sql = "SELECT {$this->_campos_lista}, ref_cod_aluno, m.aprovado
              FROM {$this->_tabela} mt
             INNER JOIN {$this->_schema}matricula m ON (m.cod_matricula = mt.ref_cod_matricula)";
        $filtros = ' WHERE m.ativo = 1 AND mt.ativo = 1 ';

        $whereAnd = ' AND ';

        if (is_numeric($escolaId)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$escolaId}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($cursoId)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$cursoId}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($serieId)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$serieId}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($turmaId)) {
            $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$turmaId}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} m.ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if ($saida_escola == 1) {
            $filtros .= "{$whereAnd} m.saida_escola = TRUE";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM  {$this->_schema}matricula_turma mt
                                       INNER JOIN {$this->_schema}matricula m ON (m.cod_matricula = mt.ref_cod_matricula) {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function listaPorSequencial($codTurma)
    {
        $db = new clsBanco();
        $sql = "
        SELECT
            nome,
            sequencial_fechamento,
            ref_cod_matricula,
            relatorio.view_situacao_relatorios.texto_situacao situacao,
            matricula_turma.id
        FROM
            cadastro.pessoa
            INNER JOIN pmieducar.aluno ON ( aluno.ref_idpes = pessoa.idpes )
            INNER JOIN pmieducar.matricula ON ( matricula.ref_cod_aluno = aluno.cod_aluno )
            INNER JOIN pmieducar.matricula_turma ON ( matricula_turma.ref_cod_matricula = matricula.cod_matricula )
            INNER JOIN pmieducar.escola ON escola.cod_escola = matricula.ref_ref_cod_escola
            INNER JOIN pmieducar.instituicao ON escola.ref_cod_instituicao = instituicao.cod_instituicao
            INNER JOIN relatorio.view_situacao_relatorios ON ( view_situacao_relatorios.cod_matricula = matricula.cod_matricula AND view_situacao_relatorios.cod_turma = matricula_turma.ref_cod_turma AND matricula_turma.sequencial = view_situacao_relatorios.sequencial )
        WHERE
            matricula_turma.ref_cod_turma = {$codTurma}
        GROUP BY
          ref_cod_matricula,
            sequencial_fechamento,
            nome,
            relatorio.view_situacao_relatorios.texto_situacao,
            matricula_turma.id

        ORDER BY
            sequencial_fechamento,
        nome";


        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla['_total'] = $this->_total;
            $resultado[] = $tupla;
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function matriculaAno($cod_aluno){
        $sql = "
        SELECT m.cod_matricula, t.nm_turma, m.ano
        FROM pmieducar.matricula AS m
        
        JOIN pmieducar.matricula_turma AS mt
            ON m.cod_matricula = mt.ref_cod_matricula
        JOIN pmieducar.turma AS t
            ON mt.ref_cod_turma = t.cod_turma        
        
        ";
        $whereAnd = ' WHERE';
        $join = '';
        $filtros = '';


        $filtros .= "{$whereAnd} ref_cod_aluno = '{$cod_aluno}' ";
        $whereAnd = 'AND ';
        $db = new clsBanco();
        $countCampos = count(explode(',',"m.cod_matricula, t.nm_turma, m.ano"));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM pmieducar.matricula AS m

        JOIN pmieducar.matricula_turma AS mt
            ON m.cod_matricula = mt.ref_cod_matricula
        JOIN pmieducar.turma AS t
            ON mt.ref_cod_turma = t.cod_turma
        
         {$filtros}
        ");
         $db->Consulta($sql);
         if ($countCampos > 1) {
             while ($db->ProximoRegistro()) {
                 $tupla = $db->Tupla();
 
                 $tupla['_total'] = $this->_total;
                 $resultado[] = $tupla;
             }
         } else {
             while ($db->ProximoRegistro()) {
                 $tupla = $db->Tupla();
                 $resultado[] = $tupla;
             }
         }
        
         if (count($resultado)) {
             return $resultado;
         }
 
         return false;
 
 
      
         

    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
            is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} mt WHERE mt.ref_cod_matricula = '{$this->ref_cod_matricula}' AND mt.ref_cod_turma = '{$this->ref_cod_turma}' AND mt.sequencial = '{$this->sequencial}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
            is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' AND sequencial = '{$this->sequencial}' AND ativo = 1 ");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna se existe alguma enturmação ativa para matrícula e turma informada.
     *
     * @return bool
     */
    public function existeEnturmacaoAtiva()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' AND ativo = 1 ");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
            is_numeric($this->ref_usuario_exc) && is_numeric($this->sequencial)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    public function buscaSequencialMax()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma)) {
            $db = new clsBanco();
            $max = $db->CampoUnico("SELECT COALESCE(MAX(sequencial),0) + 1 AS MAX FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}'");

            //removido filtro pois tornou-se possivel enturmar uma matricula em mais de uma turma
            //AND ref_cod_turma = '{$this->ref_cod_turma}'");

            return $max;
        }

        return false;
    }

    public function alunosNaoEnturmados(
        $ref_cod_escola = null,
        $ref_cod_serie = null,
        $ref_cod_curso = null,
        $ano = null
    ) {
        if ((is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) ||
            is_numeric($ref_cod_curso)) {
            $db = new clsBanco();

            $sql = "SELECT
            m.cod_matricula
          FROM
            pmieducar.matricula m
          WHERE
            m.cod_matricula NOT IN
            (
              SELECT
                ref_cod_matricula
              FROM
                {$this->_tabela} mt,
                   pmieducar.turma t
              WHERE
                t.cod_turma = mt.ref_cod_turma
                AND t.ref_ref_cod_escola = '{$ref_cod_escola}'
                AND mt.ativo = '1'
                AND t.ativo  = '1'";

            if ($ref_cod_curso) {
                $sql .= ' AND m.ref_cod_curso = t.ref_cod_curso  ';
            }

            if ($ref_cod_serie) {
                $sql .= ' AND m.ref_ref_cod_serie = t.ref_ref_cod_serie';
            }

            $sql .= ')
          AND m.ativo = \'1\'
          AND m.ultima_matricula = \'1\'
          AND
          (
            m.aprovado = \'1\'
            OR m.aprovado = \'2\'
            OR m.aprovado = \'3\'
          )';

            if ($ref_cod_curso) {
                $sql .= " AND m.ref_cod_curso = '{$ref_cod_curso}'";
            }

            if ($ref_cod_escola && $ref_cod_serie) {
                $sql .= " AND m.ref_ref_cod_serie = '{$ref_cod_serie}'
            AND m.ref_ref_cod_escola = '{$ref_cod_escola}'";
            }

            if (is_numeric($ano)) {
                $sql .= " AND m.ano = {$ano}";
            }

            $db->Consulta($sql);

            $resultado = [];
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla['cod_matricula'];
            }

            return $resultado;
        }

        return false;
    }

    public function getInstituicao()
    {
        if (is_numeric($this->ref_cod_matricula)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT ref_cod_instituicao from pmieducar.escola
                                              INNER JOIN pmieducar.matricula ON (ref_ref_cod_escola = cod_escola)
                                              WHERE cod_matricula = {$this->ref_cod_matricula}");
        }

        return false;
    }

    public function getDataSaidaEnturmacaoAnterior($ref_matricula, $sequencial)
    {
        if (is_numeric($ref_matricula) && is_numeric($sequencial)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT to_char(mtbefore.data_exclusao, 'YYYY-MM-DD')
                                      FROM {$this->_tabela} mt
                                      LEFT JOIN {$this->_tabela}  mtbefore
                                      ON mtbefore.ref_cod_matricula = mt.ref_cod_matricula
                                      AND mtbefore.sequencial = (SELECT max(sequencial) FROM {$this->_tabela} WHERE ref_cod_matricula = mt.ref_cod_matricula AND sequencial < mt.sequencial)
                                      WHERE mt.ref_cod_matricula = {$ref_matricula}
                                      AND mt.sequencial = {$sequencial}");
        }

        return false;
    }

    public function getDataEntradaEnturmacaoSeguinte($ref_matricula, $sequencial)
    {
        if (is_numeric($ref_matricula) && is_numeric($sequencial)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT to_char(mtnext.data_enturmacao, 'YYYY-MM-DD')
                                            FROM {$this->_tabela} mt
                                            LEFT JOIN {$this->_tabela} mtnext
                                            ON mtnext.ref_cod_matricula = mt.ref_cod_matricula
                                            AND mtnext.sequencial = (SELECT min(sequencial) FROM {$this->_tabela} WHERE ref_cod_matricula = mt.ref_cod_matricula AND sequencial > mt.sequencial)
                                            WHERE mt.ref_cod_matricula = {$ref_matricula}
                                              AND mt.sequencial = {$sequencial}");
        }

        return false;
    }

    public function getDataExclusaoUltimaEnturmacao(int $codMatricula)
    {
        $db = new clsBanco();

        return $db->CampoUnico("
        select
            to_char(data_exclusao, 'YYYY-MM-DD')
        from
            pmieducar.matricula_turma
        where true
            and ref_cod_matricula = $codMatricula
            and data_exclusao is not null
        order by
            data_exclusao desc
        limit 1
    ");
    }

    public function getMaiorDataEnturmacao(int $codMatricula)
    {
        $db = new clsBanco();

        return $db->CampoUnico("
        select
            to_char(data_enturmacao, 'YYYY-MM-DD')
        from
            pmieducar.matricula_turma
        where true
            and ref_cod_matricula = $codMatricula
            and data_enturmacao is not null
        order by
            data_enturmacao desc
        limit 1
    ");
    }

    public function getUltimaEnturmacao($ref_matricula)
    {
        if (is_numeric($ref_matricula)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT MAX(matricula_turma.sequencial)
                                FROM $this->_tabela
                               INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
                               INNER JOIN relatorio.view_situacao ON (view_situacao.cod_matricula = matricula.cod_matricula
                                                                      AND view_situacao.cod_turma = matricula_turma.ref_cod_turma
                                                                      AND view_situacao.sequencial = matricula_turma.sequencial)
                               WHERE ref_cod_matricula = $ref_matricula");
        }

        return false;
    }

    public function getDataBaseRemanejamento()
    {
        if ($this->ref_cod_matricula) {
            $cod_instituicao = $this->getInstituicao();
            $db = new clsBanco();

            return $db->CampoUnico("SELECT data_base_remanejamento
                                                    FROM pmieducar.instituicao WHERE cod_instituicao = {$cod_instituicao}");
        }

        return false;
    }

    public function getDataBaseTransferencia()
    {
        if ($this->ref_cod_matricula) {
            $cod_instituicao = $this->getInstituicao();
            $db = new clsBanco();

            return $db->CampoUnico("SELECT data_base_transferencia
                                                  FROM pmieducar.instituicao WHERE cod_instituicao = {$cod_instituicao}");
        }

        return false;
    }

    public function marcaAlunoRemanejado($data = null)
    {
        if ($this->ref_cod_matricula && $this->sequencial) {
            $dataBaseRemanejamento = $this->getDataBaseRemanejamento();
            $data = $data ? $data : date('Y-m-d');
            if (is_null($dataBaseRemanejamento) || strtotime($dataBaseRemanejamento) < strtotime($data)) {
                $db = new clsBanco();
                $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = true, abandono = false, reclassificado = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
            }
        }
    }

    public function marcaAlunoTransferido($data = null)
    {
        if ($this->ref_cod_matricula && $this->sequencial) {
            $dataBaseTransferencia = $this->getDataBaseTransferencia();
            $data = $data ? $data : date('Y-m-d');
            if (is_null($dataBaseTransferencia) || strtotime($dataBaseTransferencia) < strtotime($data)) {
                $db = new clsBanco();
                $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = true, remanejado = false, abandono = false, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
            } else {
                $db = new clsBanco();
                $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = true, remanejado = false, abandono = false, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
            }
        }
    }

    public function marcaAlunoReclassificado($data = null)
    {
        $data = $data ? $data : date('Y-m-d');
        if ($this->ref_cod_matricula) {
            $db = new clsBanco();
            $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = false, reclassificado = true, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND ativo = 1");
        }
    }

    public function marcaAlunoAbandono($data = null)
    {
        $data = $data ? implode('-', array_reverse(explode('/', $data))) : date('Y-m-d');
        if ($this->ref_cod_matricula && $this->sequencial) {
            $db = new clsBanco();
            $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = true, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
        }
    }

    public function marcaAlunoFalecido($data = null)
    {
        $data = $data ? implode('-', array_reverse(explode('/', $data))) : date('Y-m-d');
        if ($this->ref_cod_matricula && $this->sequencial) {
            $db = new clsBanco();
            $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = false, reclassificado = false, falecido = true, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
        }
    }

    public function dadosAlunosNaoEnturmados(
        $ref_cod_escola = null,
        $ref_cod_serie = null,
        $ref_cod_curso = null,
        $int_ano = null,
        $verificar_multiseriado = false,
        $semestre = null
    ) {
        if (is_numeric($int_ano) && (is_numeric($ref_cod_escola) ||
                is_numeric($ref_cod_serie) || is_numeric($ref_cod_curso))) {
            $db = new clsBanco();
            $complemento_sql = '';

            if ($verificar_multiseriado) {
                $complemento_sql = ', m.ref_ref_cod_escola
                  , m.cod_matricula';
            }

            $sql = "
              SELECT
                a.cod_aluno
                , p.nome
                , m.ref_ref_cod_serie
                , s.ref_cod_curso
                , s.nm_serie
                , c.nm_curso
                , f.sexo
                , f.data_nasc
                , a.tipo_responsavel
                {$complemento_sql}
              FROM
                pmieducar.matricula m
                , pmieducar.aluno a
                , cadastro.pessoa p
                , cadastro.fisica f
                , pmieducar.curso c
                , pmieducar.serie s
              WHERE
                m.cod_matricula NOT IN
                (
                  SELECT
                    ref_cod_matricula
                  FROM
                    pmieducar.matricula_turma mt
                    , pmieducar.turma t
                  WHERE
                    t.cod_turma = mt.ref_cod_turma
                    AND mt.ativo = '1'
                    AND m.ref_cod_curso = t.ref_cod_curso
                    AND m.ref_ref_cod_serie = t.ref_ref_cod_serie
                )
                AND a.ref_idpes = p.idpes
                AND p.idpes = f.idpes
                AND a.cod_aluno = m.ref_cod_aluno
                AND m.ref_ref_cod_serie = s.cod_serie
                AND s.ref_cod_curso = c.cod_curso
                AND m.ativo = '1'
                AND m.ano = '{$int_ano}'
                AND m.aprovado IN (1,2,3)
                AND m.ultima_matricula = '1'";

            if ($ref_cod_curso) {
                $sql .= " AND m.ref_cod_curso = '{$ref_cod_curso}'";
            }

            if ($ref_cod_serie) {
                $sql .= " AND m.ref_ref_cod_serie = '{$ref_cod_serie}'";
            }

            if ($ref_cod_escola) {
                $sql .= " AND m.ref_ref_cod_escola = '{$ref_cod_escola}'";
            }

            if (is_numeric($semestre)) {
                $sql .= " AND m.semestre = {$semestre} ";
            }

            $db->Consulta($sql . $this->getOrderby());

            $resultado = [];

            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                if ($verificar_multiseriado) {
                    if (is_numeric($tupla['ref_ref_cod_serie']) && is_numeric($tupla['ref_ref_cod_escola']) && is_numeric($tupla['cod_matricula'])) {
                        $sql = "SELECT
                  1
                FROM
                  pmieducar.matricula_turma mt,
                  pmieducar.turma t
                WHERE
                  mt.ativo = 1
                  AND t.ativo = 1
                  AND t.ref_ref_cod_serie_mult = {$tupla['ref_ref_cod_serie']}
                  AND t.ref_ref_cod_escola = {$tupla['ref_ref_cod_escola']}
                  AND t.cod_turma = mt.ref_cod_turma
                  AND mt.ref_cod_matricula = {$tupla['cod_matricula']}";
                        $db3 = new clsBanco();
                        $aluno_esta_enturmado = $db3->CampoUnico($sql);
                    }

                    if (!is_numeric($aluno_esta_enturmado)) {
                        $resultado[] = $tupla;
                    }
                } else {
                    $resultado[] = $tupla;
                }
            }

            return $resultado;
        }

        return false;
    }

    public function reclassificacao($data = null)
    {
        if (is_numeric($this->ref_cod_matricula)) {
            $this->marcaAlunoReclassificado($data);
            $db = new clsBanco();
            $consulta = "UPDATE {$this->_tabela} SET ativo = 0 WHERE ref_cod_matricula = '{$this->ref_cod_matricula}'";
            $db->Consulta($consulta);

            return true;
        }

        return false;
    }

    public function getAnoMatricula()
    {
        if (is_numeric($this->ref_cod_matricula)) {
            $db = new clsBanco();

            return $db->CampoUnico("SELECT ano FROM pmieducar.matricula WHERE cod_matricula = {$this->ref_cod_matricula}");
        }
    }

    public function enturmacoesSemDependencia($turmaId)
    {
        $sql = "SELECT COUNT(1) FROM {$this->_tabela} mt
              INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
              WHERE m.dependencia = 'f'
                AND mt.ativo = 1
                AND mt.ref_cod_turma = $turmaId";
        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    public function verficaEnturmacaoDeDependencia($matriculaId, $turmaId)
    {
        $sql = "SELECT 1 FROM {$this->_tabela} mt
              INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
              WHERE mt.ref_cod_matricula = $matriculaId
                AND m.dependencia = 't'
                AND mt.ativo = 1
                AND mt.ref_cod_turma = $turmaId";
        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    public function getMaxSequencialEnturmacao($matriculaId)
    {
        $db = new clsBanco();
        $sql = 'select max(sequencial) from pmieducar.matricula_turma where ref_cod_matricula = $1';

        if ($db->execPreparedQuery($sql, $matriculaId) != false) {
            $db->ProximoRegistro();
            $sequencial = $db->Tupla();

            return $sequencial[0];
        }

        return 0;
    }

    public function getUltimaTurmaEnturmacao($matriculaId)
    {
        $sequencial = $this->getMaxSequencialEnturmacao($matriculaId);
        $db = new clsBanco();
        $sql = 'select ref_cod_turma from pmieducar.matricula_turma where ref_cod_matricula = $1 and sequencial = $2';

        if ($db->execPreparedQuery($sql, [$matriculaId, $sequencial]) != false) {
            $db->ProximoRegistro();
            $ultima_turma = $db->Tupla();

            return $ultima_turma[0];
        }

        return null;
    }
}
