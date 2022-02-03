<?php

use iEducar\Legacy\Model;

class clsPmieducarMatricula extends Model
{
    public const MODELO_SEMIPRESENCIAL = 0;
    public const MODELO_EAD = 1;
    public const MODELO_OFF_LINE = 2;
    public const MODELO_PRESENCIAL = 3;

    public const MODELOS_DE_ENSINO = [
          self::MODELO_PRESENCIAL => 'Presencial' ,
          self::MODELO_SEMIPRESENCIAL => 'Semipresencial' ,
          self::MODELO_EAD => 'EAD' ,
          self::MODELO_OFF_LINE => 'Off-line' ,
    ];

    public $cod_matricula;
    public $ref_cod_reserva_vaga;
    public $ref_ref_cod_escola;
    public $ref_ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_aluno;
    public $ref_cod_abandono;
    public $aprovado;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ano;
    public $ultima_matricula;
    public $modulo;
    public $descricao_reclassificacao;
    public $matricula_reclassificacao;
    public $formando;
    public $ref_cod_curso;
    public $semestre;
    public $data_matricula;
    public $data_cancel;
    public $observacoes;
    public $turno_pre_matricula;
    public $dependencia;
    public $modalidade_ensino;
    public $nm_turma;

    /**
     * caso seja a primeira matricula do aluno
     * marcar como true este atributo
     * necessário para contabilizar como admitido por transferência
     * no relatorio de movimentacao mensal
     *
     * @var bool
     */
    public $matricula_transferencia;

    /**
     * Instância do objeto de clsBanco.
     *
     * @var clsBanco
     */
    protected $db;

    public function __construct(
        $cod_matricula = null,
        $ref_cod_reserva_vaga = null,
        $ref_ref_cod_escola = null,
        $ref_ref_cod_serie = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_cod_aluno = null,
        $aprovado = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ano = null,
        $ultima_matricula = null,
        $modulo = null,
        $formando = null,
        $descricao_reclassificacao = null,
        $matricula_reclassificacao = null,
        $ref_cod_curso = null,
        $matricula_transferencia = null,
        $semestre = null,
        $data_matricula = null,
        $data_cancel = null,
        $ref_cod_abandono = null,
        $observacoes = false,
        $modalidadeEnsino = self::MODELO_PRESENCIAL
    ) {
        $db = new clsBanco();
        $this->db = $db;
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'matricula';

        $this->_campos_lista = $this->_todos_campos = 'm.cod_matricula, m.ref_cod_reserva_vaga, m.ref_ref_cod_escola, m.ref_ref_cod_serie, m.ref_usuario_exc, m.ref_usuario_cad, m.ref_cod_aluno, m.aprovado, m.data_cadastro, m.data_exclusao, m.ativo, m.ano, m.ultima_matricula, m.modulo,formando,descricao_reclassificacao,matricula_reclassificacao, m.ref_cod_curso,m.matricula_transferencia,m.semestre, m.data_matricula, m.data_cancel, m.ref_cod_abandono_tipo, m.turno_pre_matricula, m.dependencia, data_saida_escola, m.modalidade_ensino';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($ref_cod_reserva_vaga)) {
            $this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
        }

        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }

        if (is_numeric($cod_matricula)) {
            $this->cod_matricula = $cod_matricula;
        }

        if (is_numeric($ref_ref_cod_escola)) {
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
        }

        if (is_numeric($ref_ref_cod_serie)) {
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
        }

        if (is_numeric($aprovado)) {
            $this->aprovado = $aprovado;
        }

        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }

        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }

        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        if (is_numeric($ultima_matricula)) {
            $this->ultima_matricula = $ultima_matricula;
        }

        if (is_numeric($modulo)) {
            $this->modulo = $modulo;
        }

        if (is_numeric($formando)) {
            $this->formando = $formando;
        }

        if (is_string($descricao_reclassificacao)) {
            $this->descricao_reclassificacao = $descricao_reclassificacao;
        }

        if (is_numeric($matricula_reclassificacao)) {
            $this->matricula_reclassificacao = $matricula_reclassificacao;
        }

        if (dbBool($matricula_transferencia)) {
            $this->matricula_transferencia = dbBool($matricula_transferencia) ? 't' : 'f';
        }

        if (is_numeric($semestre)) {
            $this->semestre = $semestre;
        }

        if (is_string($data_matricula)) {
            $this->data_matricula = $data_matricula;
        }

        if (is_string($data_cancel)) {
            $this->data_cancel = $data_cancel;
        }

        $this->observacoes = $observacoes;
        $this->modalidade_ensino = $modalidadeEnsino;
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (
            is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_aluno)
            && is_numeric($this->aprovado) && is_numeric($this->ano)
            && is_numeric($this->ultima_matricula) && is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_reserva_vaga)) {
                $campos .= "{$gruda}ref_cod_reserva_vaga";
                $valores .= "{$gruda}'{$this->ref_cod_reserva_vaga}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_escola)) {
                $campos .= "{$gruda}ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_serie)) {
                $campos .= "{$gruda}ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_abandono)) {
                $campos .= "{$gruda}ref_cod_abandono";
                $valores .= "{$gruda}'{$this->ref_cod_abandono}'";
                $gruda = ', ';
            }

            if (is_numeric($this->aprovado)) {
                $campos .= "{$gruda}aprovado";
                $valores .= "{$gruda}'{$this->aprovado}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ultima_matricula)) {
                $campos .= "{$gruda}ultima_matricula";
                $valores .= "{$gruda}'{$this->ultima_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->modulo)) {
                $campos .= "{$gruda}modulo";
                $valores .= "{$gruda}'{$this->modulo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->formando)) {
                $campos .= "{$gruda}formando";
                $valores .= "{$gruda}'{$this->formando}'";
                $gruda = ', ';
            }

            if (is_numeric($this->matricula_reclassificacao)) {
                $campos .= "{$gruda}matricula_reclassificacao";
                $valores .= "{$gruda}'{$this->matricula_reclassificacao}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao_reclassificacao)) {
                $descricao_recla = $db->escapeString($this->descricao_reclassificacao);
                $campos .= "{$gruda}descricao_reclassificacao";
                $valores .= "{$gruda}'{$descricao_recla}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            if (dbBool($this->matricula_transferencia)) {
                $campos .= "{$gruda}matricula_transferencia";
                $valores .= "{$gruda}'{$this->matricula_transferencia}'";
                $gruda = ', ';
            }

            if (is_numeric($this->semestre)) {
                $campos .= "{$gruda}semestre";
                $valores .= "{$gruda}'{$this->semestre}'";
                $gruda = ', ';
            }

            if (is_string($this->data_matricula)) {
                $campos .= "{$gruda}data_matricula";
                $valores .= "{$gruda}'{$this->data_matricula}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cancel)) {
                $campos .= "{$gruda}data_cancel";
                $valores .= "{$gruda}'{$this->data_cancel}'";
                $gruda = ', ';
            }

            if (is_string($this->observacoes)) {
                $observacoes = $db->escapeString($this->observacoes);
                $campos .= "{$gruda}observacoes";
                $valores .= "{$gruda}'{$observacoes}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_pre_matricula)) {
                $campos .= "{$gruda}turno_pre_matricula";
                $valores .= "{$gruda}'{$this->turno_pre_matricula}'";
            }

            if ($this->dependencia) {
                $campos .= "{$gruda}dependencia";
                $valores .= "{$gruda}true ";
                $gruda = ', ';
            }

            if (is_numeric($this->modalidade_ensino)) {
                $campos .= "{$gruda}modalidade_ensino";
                $valores .= "{$gruda}'{$this->modalidade_ensino}'";
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
            $this->cod_matricula = $db->InsertId("{$this->_tabela}_cod_matricula_seq");

            if ($this->cod_matricula) {
                $detalhe = $this->detalhe();
            }

            return $this->cod_matricula;
        }

        return false;
    }

    public function avancaModulo()
    {
        if (is_numeric($this->cod_matricula) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $db->Consulta("UPDATE {$this->_tabela} SET modulo = modulo + 1, data_exclusao = NOW(), ref_usuario_exc = '{$this->ref_usuario_exc}' WHERE cod_matricula = '{$this->cod_matricula}'");

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
        if (is_numeric($this->cod_matricula)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_reserva_vaga)) {
                $set .= "{$gruda}ref_cod_reserva_vaga = '{$this->ref_cod_reserva_vaga}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_escola)) {
                $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_serie)) {
                $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_aluno)) {
                $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_abandono)) {
                $set .= "{$gruda}ref_cod_abandono = '{$this->ref_cod_abandono}'";
                $gruda = ', ';
            }

            if (is_numeric($this->aprovado)) {
                $set .= "{$gruda}aprovado = '{$this->aprovado}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ultima_matricula)) {
                $set .= "{$gruda}ultima_matricula = '{$this->ultima_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->modulo)) {
                $set .= "{$gruda}modulo = '{$this->modulo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->formando)) {
                $set .= "{$gruda}formando = '{$this->formando}'";
                $gruda = ', ';
            }

            if (is_numeric($this->matricula_reclassificacao)) {
                $set .= "{$gruda}matricula_reclassificacao = '{$this->matricula_reclassificacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_curso)) {
                $set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao_reclassificacao)) {
                $descricao_recla = $db->escapeString($this->descricao_reclassificacao);
                $set .= "{$gruda}descricao_reclassificacao = '{$descricao_recla}'";
                $gruda = ', ';
            }

            if (is_numeric($this->semestre)) {
                $set .= "{$gruda}semestre = '{$this->semestre}'";
                $gruda = ', ';
            }

            if (is_string($this->data_matricula)) {
                $set .= "{$gruda}data_matricula = '{$this->data_matricula}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cancel)) {
                $set .= "{$gruda}data_cancel = '{$this->data_cancel}'";
                $gruda = ', ';
            } elseif (is_null($this->data_cancel)) {
                $set .= "{$gruda}data_cancel = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->turno_pre_matricula)) {
                $set .= "{$gruda}turno_pre_matricula = '{$this->turno_pre_matricula}'";
                $gruda = ', ';
            }

            if ($this->dependencia) {
                $set .= "{$gruda}dependencia = true ";
                $gruda = ', ';
            }

            if (is_string($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
                $gruda = ', ';
            } elseif (is_null($this->data_exclusao)) {
                $set .= "{$gruda}data_exclusao = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->modalidade_ensino)) {
                $set .= "{$gruda}modalidade_ensino = '{$this->modalidade_ensino}'";
                $gruda = ', ';
            }

            if (is_string($this->observacoes)) {
                $observacoes = $db->escapeString($this->observacoes);
                $set .= "{$gruda}observacoes = '{$observacoes}'";
            } elseif ($this->observacoes !== false) {
                $set .= "{$gruda}observacoes = NULL";
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_matricula = '{$this->cod_matricula}'");

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
        $int_cod_matricula = null,
        $int_ref_cod_reserva_vaga = null,
        $int_ref_ref_cod_escola = null,
        $int_ref_ref_cod_serie = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $ref_cod_aluno = null,
        $int_aprovado = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ano = null,
        $int_ref_cod_curso2 = null,
        $int_ref_cod_instituicao = null,
        $int_ultima_matricula = null,
        $int_modulo = null,
        $int_padrao_ano_escolar = null,
        $int_analfabeto = null,
        $int_formando = null,
        $str_descricao_reclassificacao = null,
        $int_matricula_reclassificacao = null,
        $boo_com_deficiencia = null,
        $int_ref_cod_curso = null,
        $bool_curso_sem_avaliacao = null,
        $arr_int_cod_matricula = null,
        $int_mes_defasado = null,
        $boo_data_nasc = null,
        $boo_matricula_transferencia = null,
        $int_semestre = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_abandono = null,
        $matriculas_turmas_transferidas_abandono = false,
        $data_saida_escola = null
    ) {
        if ($boo_data_nasc) {
            $this->_campos_lista .= ' ,(SELECT data_nasc
                                          FROM cadastro.fisica
                                         WHERE idpes = ref_idpes) as data_nasc';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $condicao_sequencial_fechamento = "AND ref_cod_turma = {$int_ref_cod_turma}";
        } else {
            $condicao_sequencial_fechamento = 'AND ativo = 1';
        }

        $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, p.nome, a.cod_aluno, a.ref_idpes, c.cod_curso, m.observacao, s.nm_serie, (SELECT sequencial_fechamento FROM pmieducar.matricula_turma WHERE ref_cod_matricula = cod_matricula {$condicao_sequencial_fechamento} LIMIT 1) as sequencial_fechamento FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a,  {$this->_schema}serie s, cadastro.pessoa p ";
        $whereAnd = ' AND ';
        $filtros = ' WHERE m.ref_cod_aluno = a.cod_aluno AND m.ref_cod_curso = c.cod_curso AND p.idpes = a.ref_idpes AND m.ref_ref_cod_serie = s.cod_serie ';

        if (is_numeric($int_cod_matricula)) {
            $filtros .= "{$whereAnd} m.cod_matricula = '{$int_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_reserva_vaga)) {
            $filtros .= "{$whereAnd} m.ref_cod_reserva_vaga = '{$int_ref_cod_reserva_vaga}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} m.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} m.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$ref_cod_aluno}'";
            $whereAnd = ' AND ';
        } elseif (is_array($ref_cod_aluno)) {
            $ref_cod_aluno = implode(', ', $ref_cod_aluno);
            $filtros .= "{$whereAnd} m.ref_cod_aluno in ({$ref_cod_aluno}) ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_aprovado)) {
            $filtros .= "{$whereAnd} m.aprovado = '{$int_aprovado}'";
            $whereAnd = ' AND ';
        } elseif (is_array($int_aprovado)) {
            $int_aprovado = implode(',', $int_aprovado);
            $filtros .= "{$whereAnd} m.aprovado in ({$int_aprovado})";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} m.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} m.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} m.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} m.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if ($int_ativo) {
            $filtros .= "{$whereAnd} m.ativo = '1' AND a.ativo = '1' ";
            $whereAnd = ' AND ';
        } elseif (!is_null($int_ativo) && is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} m.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} m.ano = '{$int_ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_padrao_ano_escolar)) {
            $filtros .= "{$whereAnd} c.padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ultima_matricula)) {
            $filtros .= "{$whereAnd} ultima_matricula = '{$int_ultima_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_modulo)) {
            $filtros .= "{$whereAnd} m.modulo = '{$int_modulo}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_analfabeto)) {
            $filtros .= "{$whereAnd} a.analfabeto = '{$int_analfabeto}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_formando)) {
            $filtros .= "{$whereAnd} a.formando = '{$int_formando}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_matricula_reclassificacao)) {
            $filtros .= "{$whereAnd} m.matricula_reclassificacao = '{$int_matricula_reclassificacao}'";
            $whereAnd = ' AND ';
        }

        if (dbBool($boo_matricula_transferencia)) {
            $boo_matricula_transferencia = dbBool($boo_matricula_transferencia) ? 't' : 'f';
            $filtros .= "{$whereAnd} m.matricula_transferencia = '{$boo_matricula_transferencia}'";
            $whereAnd = ' AND ';
        }

        if (is_string($int_matricula_reclassificacao)) {
            $filtros .= "{$whereAnd} (a.matricula_reclassificacao) like ('%{$int_matricula_reclassificacao}%')";
            $whereAnd = ' AND ';
        }
        if (is_bool($boo_com_deficiencia)) {
            $not = $boo_com_deficiencia === true ? '' : 'NOT';
            $filtros .= "{$whereAnd} $not EXISTS (SELECT 1 FROM cadastro.fisica_deficiencia fd, pmieducar.aluno a WHERE a.cod_aluno = m.ref_cod_aluno AND fd.ref_idpes = a.ref_idpes)";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_semestre)) {
            $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            if ($matriculas_turmas_transferidas_abandono) {
                $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                                   FROM pmieducar.matricula_turma mt
                                                  WHERE ((mt.ativo = 1) OR (NOT EXISTS (SELECT 1
                                                                                          FROM pmieducar.matricula_turma sub_mt
                                                                                    INNER JOIN pmieducar.matricula sub_m ON (sub_m.cod_matricula = sub_mt.ref_cod_matricula)
                                                                                         WHERE sub_mt.ativo = 1
                                                                                           AND sub_m.ref_cod_aluno = a.cod_aluno
                                                                                           AND sub_mt.ref_cod_turma = {$int_ref_cod_turma})))
                                                    AND mt.ref_cod_turma = {$int_ref_cod_turma}
                                                    AND mt.ref_cod_matricula = m.cod_matricula)";
            } else {
                $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.matricula_turma mt WHERE mt.ativo = 1 AND mt.ref_cod_turma = {$int_ref_cod_turma} AND mt.ref_cod_matricula = m.cod_matricula)";
            }

            $whereAnd = ' AND ';
        }

        if (is_array($arr_int_cod_matricula) && count($arr_int_cod_matricula)) {
            $filtros .= "{$whereAnd} cod_matricula IN (" . implode(',', $arr_int_cod_matricula) . ')';
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_mes_defasado)) {
            $primeiroDiaDoMes = mktime(0, 0, 0, $int_mes_defasado, 1, $int_ano);
            $NumeroDiasMes = date('t', $primeiroDiaDoMes);
            $ultimoDiaMes = date('d/m/Y', mktime(0, 0, 0, $int_mes_defasado, $NumeroDiasMes, $int_ano));
            $ultimoDiaMes = dataToBanco($ultimoDiaMes, false);
            $primeiroDiaDoMes = date('d/m/Y', $primeiroDiaDoMes);
            $primeiroDiaDoMes = dataToBanco($primeiroDiaDoMes, false);
            $filtroAux = "{$whereAnd} ((aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
                                       OR
                                      (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes'))";
            $filtros .= $filtroAux;
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, {$this->_schema}serie s, cadastro.pessoa p {$filtros}");
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

    public function lista_transferidos(
        $int_cod_matricula = null,
        $int_ref_cod_reserva_vaga = null,
        $int_ref_ref_cod_escola = null,
        $int_ref_ref_cod_serie = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $ref_cod_aluno = null,
        $int_aprovado = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ano = null,
        $int_ref_cod_curso2 = null,
        $int_ref_cod_instituicao = null,
        $int_ultima_matricula = null,
        $int_modulo = null,
        $int_padrao_ano_escolar = null,
        $int_analfabeto = null,
        $int_formando = null,
        $str_descricao_reclassificacao = null,
        $int_matricula_reclassificacao = null,
        $boo_com_deficiencia = null,
        $int_ref_cod_curso = null,
        $bool_curso_sem_avaliacao = null,
        $arr_int_cod_matricula = null,
        $int_mes_defasado = null,
        $boo_data_nasc = null,
        $boo_matricula_transferencia = null,
        $int_semestre = null,
        $int_ref_cod_turma = null,
        $int_ref_cod_abandono = null,
        $matriculas_turmas_transferidas_abandono = false
    ) {
        if ($boo_data_nasc) {
            $this->_campos_lista .= ' ,(SELECT data_nasc
                                          FROM cadastro.fisica
                                         WHERE idpes = ref_idpes
                                       ) as data_nasc';
        }

        if (is_numeric($int_ref_cod_turma)) {
            $condicao_sequencial_fechamento = "AND ref_cod_turma = {$int_ref_cod_turma}";
        } else {
            $condicao_sequencial_fechamento = 'AND ativo = 1';
        }

        $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, p.nome, a.cod_aluno, a.ref_idpes, c.cod_curso, m.observacao, (SELECT sequencial_fechamento FROM pmieducar.matricula_turma WHERE ref_cod_matricula = cod_matricula {$condicao_sequencial_fechamento} LIMIT 1) as sequencial_fechamento FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p ";
        $whereAnd = ' AND ';
        $filtros = ' WHERE m.ref_cod_aluno = a.cod_aluno AND m.ref_cod_curso = c.cod_curso AND p.idpes = a.ref_idpes ';

        if (is_numeric($int_cod_matricula)) {
            $filtros .= "{$whereAnd} m.cod_matricula = '{$int_cod_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_reserva_vaga)) {
            $filtros .= "{$whereAnd} m.ref_cod_reserva_vaga = '{$int_ref_cod_reserva_vaga}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} m.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} m.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_aluno)) {
            $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$ref_cod_aluno}'";
            $whereAnd = ' AND ';
        } elseif (is_array($ref_cod_aluno)) {
            $ref_cod_aluno = implode(', ', $ref_cod_aluno);
            $filtros .= "{$whereAnd} m.ref_cod_aluno in ({$ref_cod_aluno}) ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_aprovado)) {
            $filtros .= "{$whereAnd} m.aprovado = '{$int_aprovado}'";
            $whereAnd = ' AND ';
        } elseif (is_array($int_aprovado)) {
            $int_aprovado = implode(',', $int_aprovado);
            $filtros .= "{$whereAnd} m.aprovado in ({$int_aprovado})";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} m.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} m.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} m.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} m.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if ($int_ativo) {
            $filtros .= "{$whereAnd} m.ativo = '1' AND a.ativo = '1' ";
            $whereAnd = ' AND ';
        } elseif (!is_null($int_ativo) && is_numeric($int_ativo)) {
            $filtros .= "{$whereAnd} m.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} m.ano = '{$int_ano}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_padrao_ano_escolar)) {
            $filtros .= "{$whereAnd} c.padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ultima_matricula)) {
            $filtros .= "{$whereAnd} ultima_matricula = '{$int_ultima_matricula}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_modulo)) {
            $filtros .= "{$whereAnd} m.modulo = '{$int_modulo}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_analfabeto)) {
            $filtros .= "{$whereAnd} a.analfabeto = '{$int_analfabeto}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_formando)) {
            $filtros .= "{$whereAnd} a.formando = '{$int_formando}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_matricula_reclassificacao)) {
            $filtros .= "{$whereAnd} m.matricula_reclassificacao = '{$int_matricula_reclassificacao}'";
            $whereAnd = ' AND ';
        }

        if (dbBool($boo_matricula_transferencia)) {
            $boo_matricula_transferencia = dbBool($boo_matricula_transferencia) ? 't' : 'f';
            $filtros .= "{$whereAnd} m.matricula_transferencia = '{$boo_matricula_transferencia}'";
            $whereAnd = ' AND ';
        }

        if (is_string($int_matricula_reclassificacao)) {
            $filtros .= "{$whereAnd} (a.matricula_reclassificacao) like ('%{$int_matricula_reclassificacao}%')";
            $whereAnd = ' AND ';
        }

        if (is_bool($boo_com_deficiencia)) {
            $not = $boo_com_deficiencia === true ? '' : 'NOT';
            $filtros .= "{$whereAnd} $not EXISTS (SELECT 1 FROM cadastro.fisica_deficiencia fd, pmieducar.aluno a WHERE a.cod_aluno = m.ref_cod_aluno AND fd.ref_idpes = a.ref_idpes)";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_semestre)) {
            $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_turma)) {
            if ($matriculas_turmas_transferidas_abandono) {
                $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                           FROM pmieducar.matricula_turma mt
                                          WHERE ((mt.ativo = 1) OR (NOT EXISTS (SELECT 1
                                                                                  FROM pmieducar.matricula_turma sub_mt
                                                                            INNER JOIN pmieducar.matricula sub_m ON (sub_m.cod_matricula = sub_mt.ref_cod_matricula)
                                                                                 WHERE sub_mt.ativo = 1
                                                                                   AND sub_m.ref_cod_aluno = a.cod_aluno
                                                                                   AND sub_mt.ref_cod_turma = {$int_ref_cod_turma})))
                                            AND mt.ref_cod_turma = {$int_ref_cod_turma}
                                            AND mt.ref_cod_matricula = m.cod_matricula)";
            } else {
                $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.matricula_turma mt WHERE mt.ref_cod_turma = {$int_ref_cod_turma} AND mt.ref_cod_matricula = m.cod_matricula)";
            }

            $whereAnd = ' AND ';
        }

        if (is_array($arr_int_cod_matricula) && count($arr_int_cod_matricula)) {
            $filtros .= "{$whereAnd} cod_matricula IN (" . implode(',', $arr_int_cod_matricula) . ')';
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_mes_defasado)) {
            $primeiroDiaDoMes = mktime(0, 0, 0, $int_mes_defasado, 1, $int_ano);
            $NumeroDiasMes = date('t', $primeiroDiaDoMes);
            $ultimoDiaMes = date('d/m/Y', mktime(0, 0, 0, $int_mes_defasado, $NumeroDiasMes, $int_ano));
            $ultimoDiaMes = dataToBanco($ultimoDiaMes, false);
            $primeiroDiaDoMes = date('d/m/Y', $primeiroDiaDoMes);
            $primeiroDiaDoMes = dataToBanco($primeiroDiaDoMes, false);
            $filtroAux = "{$whereAnd} ((aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
                                       OR
                                       (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes'))";
            $filtros .= $filtroAux;
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p {$filtros}");
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
    

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_matricula)) {
            $sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper, e.ref_cod_instituicao FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p, {$this->_schema}escola e WHERE m.cod_matricula = '{$this->cod_matricula}' AND a.cod_aluno = m.ref_cod_aluno AND p.idpes = a.ref_idpes AND m.ref_ref_cod_escola = e.cod_escola ";

            if ($this->ativo) {
                $sql .= " AND m.ativo = {$this->ativo}";
            }

            if ($this->ultima_matricula) {
                $sql .= " AND m.ultima_matricula = {$this->ultima_matricula}";
            }

            $db = new clsBanco();
            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        if (!$this->cod_matricula && is_numeric($this->ref_ref_cod_escola)) {
            $sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p WHERE m.ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
            $db = new clsBanco();
            $db->Consulta($sql);
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
        if (is_numeric($this->cod_matricula)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_matricula = '{$this->cod_matricula}'");
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
        $codAluno = $_GET['ref_cod_aluno'];

        if (is_numeric($this->cod_matricula) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;
            $this->ultima_matricula = 0;

            $db = new clsBanco();

            $existeTransfereciaSolicitacao = $db->CampoUnico("SELECT max(transferencia_solicitacao.cod_transferencia_solicitacao)
                                                                FROM pmieducar.matricula
                                                          INNER JOIN pmieducar.transferencia_solicitacao ON (matricula.cod_matricula = transferencia_solicitacao.ref_cod_matricula_saida)
                                                               WHERE matricula.ativo = 1
                                                                 AND matricula.ref_cod_aluno = $codAluno
                                                                 AND matricula.cod_matricula <> $this->cod_matricula
                                                                 AND matricula.aprovado = 4");

            if (!is_null($existeTransfereciaSolicitacao)) {
                $getCodMatriculaTransferido = $db->CampoUnico("SELECT max(cod_matricula) FROM pmieducar.matricula WHERE aprovado = 4 AND ref_cod_aluno = $codAluno");

                $db->Consulta("UPDATE pmieducar.transferencia_solicitacao
                                  SET ativo = 1
                                WHERE ref_cod_matricula_saida = $getCodMatriculaTransferido");
            }

            return $this->edita();
        }

        return false;
    }

    public function verificaMatriculaUltimoAno($codAluno, $codMatricula)
    {
        $db = new clsBanco();

        $ultimoAnoMatricula = $db->CampoUnico("SELECT MAX(matricula.ano)
                                                 FROM pmieducar.matricula
                                                WHERE matricula.ref_cod_aluno = $codAluno
                                                  AND matricula.ativo = 1");

        $anoMatricula = $db->CampoUnico("SELECT matricula.ano
                                           FROM pmieducar.matricula
                                          WHERE matricula.cod_matricula = $codMatricula
                                            AND matricula.ativo = 1");

        if ($ultimoAnoMatricula == $anoMatricula) {
            return true;
        }

        return false;
    }

    public function getDadosUltimaMatricula($codAluno)
    {
        $db = new clsBanco();

        $ultimaMatricula = $db->CampoUnico("SELECT MAX(matricula.cod_matricula)
                                              FROM pmieducar.matricula
                                             WHERE matricula.ref_cod_aluno = $codAluno
                                               AND matricula.ativo = 1");

        $sql = $ultimaMatricula == null ? null :
            "SELECT *
              FROM pmieducar.matricula
             WHERE matricula.ref_cod_aluno = $codAluno
               AND matricula.ativo = 1
               AND matricula.cod_matricula = $ultimaMatricula";

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $dadosUltimaMatricula[] = $tupla;
        }

        return $dadosUltimaMatricula;
    }

    public function getEndMatricula($codAluno)
    {
        $db = new clsBanco();
        $situacaoUltimaMatricula = $db->CampoUnico("SELECT matricula.aprovado
                                                      FROM pmieducar.matricula
                                                     WHERE matricula.ref_cod_aluno = $codAluno
                                                       AND matricula.ativo = 1
                                                       AND matricula.cod_matricula = (SELECT max(m.cod_matricula)
                                                                                        FROM pmieducar.matricula AS m
                                                                                       WHERE m.ref_cod_aluno = matricula.ref_cod_aluno
                                                                                         AND m.ativo = 1)");

        return $situacaoUltimaMatricula;
    }

    public function isSequencia($origem, $destino)
    {
        $obj = new clsPmieducarSequenciaSerie();
        $sequencia = $obj->lista($origem, null, null, null, null, null, null, null, 1);
        $achou = false;

        if ($sequencia) {
            do {
                if ($lista['ref_serie_origem'] == $destino) {
                    $achou = true;
                    break;
                }
                if ($lista['ref_serie_destino'] == $destino) {
                    $achou = true;
                    break;
                }

                $sequencia_ = $obj->lista(
                    $lista['ref_serie_destino'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    1
                );

                if (!$lista) {
                    $achou = false;
                    break;
                }
            } while ($achou != false);
        }

        return $achou;
    }

    public function getInicioSequencia()
    {
        $db = new clsBanco();
        $sql = 'SELECT o.ref_serie_origem
                  FROM pmieducar.sequencia_serie o
                 WHERE NOT EXISTS (SELECT 1
                                     FROM pmieducar.sequencia_serie d
                                    WHERE o.ref_serie_origem = d.ref_serie_destino)';

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla;
        }

        return $resultado;
    }

    public function getFimSequencia()
    {
        $db = new clsBanco();
        $sql = 'SELECT o.ref_serie_destino
                  FROM pmieducar.sequencia_serie o
                 WHERE NOT EXISTS (SELECT 1
                                     FROM pmieducar.sequencia_serie d
                                    WHERE o.ref_serie_destino = d.ref_serie_origem)';

        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla;
        }

        return $resultado;
    }

    /**
     * Retorna os dados de um registro.
     *
     * @return array
     */
    public function numModulo(
        $int_ref_ref_cod_serie,
        $int_ref_ref_cod_escola,
        $int_ref_ref_cod_turma,
        $int_ref_cod_turma,
        $int_ref_ref_cod_matricula
    ) {
        $db = new clsBanco();
        $sql = "SELECT CASE WHEN FLOOR((SELECT COUNT(*)
                                          FROM pmieducar.nota_aluno
                                         WHERE disc_ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                                           AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                                           AND disc_ref_cod_turma = {$int_ref_ref_cod_turma}
                                           AND ref_ref_cod_matricula = {$int_ref_ref_cod_matricula}
                                           AND ref_ref_cod_turma = {$int_ref_cod_turma}) / ((SELECT COUNT(*)
                                                                                               FROM pmieducar.disciplina_serie
                                                                                              WHERE ref_cod_serie = {$int_ref_ref_cod_serie}) - (SELECT COUNT(0)
                                                                                                                                                   FROM pmieducar.dispensa_disciplina
                                                                                                                                                  WHERE ref_ref_cod_turma = {$int_ref_cod_turma}
                                                                                                                                                    AND ref_ref_cod_matricula = {$int_ref_ref_cod_matricula}
                                                                                                                                                    AND disc_ref_ref_cod_turma = {$int_ref_ref_cod_turma}
                                                                                                                                                    AND disc_ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                                                                                                                                                    AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}))) = 0
                            THEN 0
                       ELSE FLOOR((SELECT COUNT(*)
                                     FROM pmieducar.nota_aluno
                                    WHERE disc_ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                                      AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                                      AND disc_ref_cod_turma = {$int_ref_ref_cod_turma}
                                      AND ref_ref_cod_matricula = {$int_ref_ref_cod_matricula}
                                      AND ref_ref_cod_turma = {$int_ref_cod_turma}) / ((SELECT COUNT(*)
                                                                                          FROM pmieducar.disciplina_serie
                                                                                         WHERE ref_cod_serie = {$int_ref_ref_cod_serie}) - (SELECT COUNT(0)
                                                                                                                                              FROM pmieducar.dispensa_disciplina
                                                                                                                                             WHERE ref_ref_cod_turma = {$int_ref_cod_turma}
                                                                                                                                               AND ref_ref_cod_matricula = {$int_ref_ref_cod_matricula}
                                                                                                                                               AND disc_ref_ref_cod_turma = {$int_ref_ref_cod_turma}
                                                                                                                                               AND disc_ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                                                                                                                                               AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola})))
                END";

        return $db->CampoUnico($sql);
    }

    /**
     * Seta a matricula para abandono e seta a observação passada por parâmetro
     *
     * @return boolean
     *
     * @author lucassch
     *
     */
    public function cadastraObs($obs, $tipoAbandono)
    {
        if (is_numeric($this->cod_matricula)) {
            if (trim($obs) == '') {
                $obs = 'Não informado';
            } elseif (is_string($obs)) {
                $obs = pg_escape_string($obs);
            }

            $db = new clsBanco();
            $consulta = "UPDATE {$this->_tabela}
                            SET aprovado = 6,
                                observacao = '$obs',
                                ref_cod_abandono_tipo = '$tipoAbandono'
                          WHERE cod_matricula = $this->cod_matricula";

            $db->Consulta($consulta);

            return true;
        }

        return false;
    }

    public function cadastraObservacaoFalecido($observacao = null)
    {
        if (is_numeric($this->cod_matricula)) {
            if (trim($observacao) == '' || is_null($observacao)) {
                $observacao = 'Não informado';
            } else {
                $observacao = pg_escape_string($observacao);
            }

            $db = new clsBanco();
            $sql = "UPDATE {$this->_tabela}
                       SET aprovado = 15,
                           observacao = '$observacao'
                     WHERE cod_matricula = $this->cod_matricula";

            $db->Consulta($sql);
            $this->setPessoaFalecido();

            return true;
        }

        return false;
    }

    public function setPessoaFalecido()
    {
        if (!is_numeric($this->cod_matricula)) {
            return false;
        }

        $matricula = new clsPmieducarMatricula($this->cod_matricula);
        $matricula = $this->detalhe();

        $aluno = new clsPmieducarAluno($matricula['ref_cod_aluno']);
        $aluno = $aluno->detalhe();

        $pessoaFisica = new clsFisica($aluno['ref_idpes']);
        $pessoaFisica->falecido = true;
        $pessoaFisica->edita();
    }

    public function existeSaidaEscola($codMatricula)
    {
        if (is_numeric($codMatricula)) {
            $db = new clsBanco();
            $sql = "SELECT saida_escola
                      FROM {$this->_tabela}
                     WHERE cod_matricula = $codMatricula";

            $saida = $db->CampoUnico($sql);

            return dbBool($saida);
        }
    }

    public function setSaidaEscola($observacao = null, $data = null)
    {
        if (is_numeric($this->cod_matricula)) {
            if (trim($observacao) == '' || is_null($observacao)) {
                $observacao = 'Não informado';
            }

            $db = new clsBanco();
            $sql = "UPDATE {$this->_tabela}
                       SET saida_escola = true,
                           observacao = '$observacao',
                           data_saida_escola = '$data'
                     WHERE cod_matricula = $this->cod_matricula";

            $db->Consulta($sql);

            return true;
        }

        return false;
    }

    public function aprova_matricula_andamento_curso_sem_avaliacao()
    {
        if (is_numeric($this->ref_ref_cod_escola)) {
            $db = new clsBanco();
            $consulta = "UPDATE {$this->_tabela} SET aprovado = 1 , ref_usuario_exc = {$this->ref_usuario_exc} , data_exclusao = NOW() WHERE ano = {$this->ano} AND ref_ref_cod_escola = {$this->ref_ref_cod_escola} AND exists (SELECT 1 FROM {$this->_schema}curso c WHERE c.cod_curso = ref_cod_curso)";
            $db->Consulta($consulta);

            return true;
        }

        return false;
    }

    public function matriculaAlunoAndamento($aluno, $anoLetivo, $showErrors = true)
    {
        if ($aluno && $anoLetivo) {
            $sql = 'SELECT cod_matricula,
                           ref_cod_aluno AS cod_aluno
                      FROM pmieducar.matricula
                     WHERE ativo = 1
                       AND aprovado = 3
                       AND ano = $1
                       AND ref_cod_aluno = $2';

            $options = [
                'params' => [$anoLetivo, $aluno],
                'show_errors' => !$showErrors,
                'return_only' => 'first-line',
                'messenger' => ''
            ];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }

        return false;
    }

    public function getTotalAlunosEscola(
        $cod_escola,
        $cod_curso,
        $cod_serie,
        $ano = null,
        $semestre = null
    ) {
        if (is_numeric($cod_escola) && is_numeric($cod_curso)) {
            if (!is_numeric($ano)) {
                $ano = date('Y');
            }

            if (is_numeric($cod_serie)) {
                $where = " AND ref_ref_cod_serie = {$cod_serie} ";
            }

            if (is_numeric($semestre)) {
                $where .= " AND semestre = {$semestre} ";
            }

            $select = "SELECT count(1) as total_alunos_serie,
                              ref_ref_cod_serie as cod_serie,
                              nm_serie
                         FROM pmieducar.matricula,
                              pmieducar.serie
                        WHERE serie.cod_serie = ref_ref_cod_serie
                          AND ref_ref_cod_escola = {$cod_escola}
                          AND serie.ref_cod_curso = {$cod_curso}
                          AND ano = {$ano}
                          $where
                          AND ultima_matricula = 1
                          AND aprovado IN (1,2,3)
                          AND matricula.ativo = 1
                     GROUP BY ref_ref_cod_serie,
                              ref_ref_cod_escola,
                              nm_serie";

            $db = new clsBanco();
            $db->Consulta($select);
            $total_registros = $db->numLinhas();

            if (!$total_registros) {
                return false;
            }

            $resultados = [];
            $total = 0;

            while ($db->ProximoRegistro()) {
                $registro = $db->Tupla();
                $total += $registro['total_alunos_serie'];
                $resultados[$registro['cod_serie']] = $registro;
            }

            $array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();
            $db = new clsBanco();

            foreach ($array_inicio_sequencias as $serie_inicio) {
                $serie_inicio = $serie_inicio[0];
                $seq_ini = $serie_inicio;
                $seq_correta = false;
                $series[$cod_serie] = $cod_serie;

                do {
                    $sql = "SELECT o.ref_serie_origem,
                                   s.nm_serie,
                                   o.ref_serie_destino,
                                   s.ref_cod_curso as ref_cod_curso_origem,
                                   sd.ref_cod_curso as ref_cod_curso_destino
                              FROM pmieducar.sequencia_serie o,
                                   pmieducar.serie s,
                                   pmieducar.serie sd
                             WHERE s.cod_serie = o.ref_serie_origem
                               AND s.cod_serie = $seq_ini
                               AND sd.cod_serie = o.ref_serie_destino";

                    $db->Consulta($sql);
                    $db->ProximoRegistro();
                    $tupla = $db->Tupla();
                    $serie_origem = $tupla['ref_serie_origem'];
                    $seq_ini = $serie_destino = $tupla['ref_serie_destino'];
                    $series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];
                    $sql = "SELECT 1
                              FROM pmieducar.sequencia_serie s
                             WHERE s.ref_serie_origem = $seq_ini";

                    $true = $db->CampoUnico($sql);
                } while ($true);

                $obj_serie = new clsPmieducarSerie($serie_destino);
                $det_serie = $obj_serie->detalhe();

                if ($cod_serie == $serie_destino) {
                    $seq_correta = true;
                }
            }

            if ($series) {
                $resultados2 = [];

                foreach ($series as $key => $serie) {
                    if (key_exists($key, $resultados)) {
                        $resultados[$key]['_total'] = $total;
                        $resultados2[] = $resultados[$key];
                    }
                }
            }

            return $resultados2;
        }

        return false;
    }

    public function getTotalAlunosIdadeSexoEscola(
        $cod_escola,
        $cod_curso,
        $cod_serie,
        $ano = null,
        $semestre = null
    ) {
        if (is_numeric($cod_escola) && is_numeric($cod_curso)) {
            if (!is_numeric($ano)) {
                $ano = date('Y');
            }

            if (is_numeric($cod_serie)) {
                $where = " AND ref_ref_cod_serie = {$cod_serie} ";
            }

            if (is_numeric($semestre)) {
                $where .= " AND m.semestre = {$semestre} ";
            }

            $select = "SELECT m.ref_ref_cod_serie as cod_serie,
                              nm_serie,
                              COUNT(1) as total_alunos_serie,
                              COALESCE ( EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )::text , '-' ) as idade,
                              f.sexo
                         FROM pmieducar.aluno a,
                              pmieducar.matricula m,
                              cadastro.fisica f,
                              pmieducar.serie
                        WHERE a.cod_aluno = m.ref_cod_aluno
                          AND a.ref_idpes = idpes
                          AND ref_ref_cod_serie = cod_serie
                          AND m.ref_ref_cod_escola = $cod_escola
                          AND ano = $ano
                          AND ultima_matricula = 1
                          AND aprovado IN ( 1,2,3)
                          AND m.ref_cod_curso = $cod_curso
                          $where
                     GROUP BY m.ref_ref_cod_serie,
                              nm_serie,
                              EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) ),
                              f.sexo
                     ORDER BY EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) ),
                              f.sexo";

            $db = new clsBanco();
            $db->Consulta($select);
            $total_registros = $db->numLinhas();

            if (!$total_registros) {
                return false;
            }

            $resultados = [];
            $total = 0;

            while ($db->ProximoRegistro()) {
                $registro = $db->Tupla();
                $total += $registro['total_alunos_serie'];
                $resultados[] = $registro;
            }

            $array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();
            $db = new clsBanco();

            foreach ($array_inicio_sequencias as $serie_inicio) {
                $serie_inicio = $serie_inicio[0];
                $seq_ini = $serie_inicio;
                $seq_correta = false;
                $series[$cod_serie] = $cod_serie;
                do {
                    $sql = "SELECT o.ref_serie_origem,
                                   s.nm_serie,
                                   o.ref_serie_destino,
                                   s.ref_cod_curso as ref_cod_curso_origem,
                                   sd.ref_cod_curso as ref_cod_curso_destino
                              FROM pmieducar.sequencia_serie o
                                   pmieducar.serie s
                                   pmieducar.serie sd
                             WHERE s.cod_serie = o.ref_serie_origem
                               AND s.cod_serie = $seq_ini
                               AND sd.cod_serie = o.ref_serie_destino";

                    $db->Consulta($sql);
                    $db->ProximoRegistro();
                    $tupla = $db->Tupla();
                    $serie_origem = $tupla['ref_serie_origem'];
                    $seq_ini = $serie_destino = $tupla['ref_serie_destino'];
                    $series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];
                    $sql = "SELECT 1
                              FROM pmieducar.sequencia_serie s
                             WHERE s.ref_serie_origem = $seq_ini";

                    $true = $db->CampoUnico($sql);
                } while ($true);

                $obj_serie = new clsPmieducarSerie($serie_destino);
                $det_serie = $obj_serie->detalhe();

                if ($cod_serie == $serie_destino) {
                    $seq_correta = true;
                }
            }

            if ($series) {
                $resultados2 = [];

                foreach ($series as $key => $serie) {
                    foreach ($resultados as $key2 => $resultado) {
                        if ($key == $resultado['cod_serie']) {
                            $resultados[$key2]['_total'] = $total;
                            $resultados2[] = $resultados[$key2];
                            unset($resultados[$key2]);
                        }
                    }
                }
            }

            return $resultados2;
        }

        return false;
    }

    public function pegaDataDeTransferencia($cod_aluno, $ano)
    {
        $query = "
            select
                matricula.data_cancel
            from
                pmieducar.matricula
            inner join pmieducar.transferencia_solicitacao
                on transferencia_solicitacao.ref_cod_matricula_saida = matricula.cod_matricula
            where true
                and transferencia_solicitacao.ativo = 1
                and matricula.ref_cod_aluno = {$cod_aluno}
                and matricula.ano = {$ano}
                and matricula.aprovado = 4
            order by
                transferencia_solicitacao.cod_transferencia_solicitacao desc
            limit 1;
        ";

        $data = $this->db->UnicoCampo($query);

        if (!$data) {
            return false;
        }

        return new \DateTime($data);
    }

    public function pegaDataAnoLetivoInicio($cod_turma)
    {
        $query = "
            select
                CASE WHEN curso.padrao_ano_escolar = 1
                    THEN ano_letivo_modulo.data_inicio
                ELSE
                    turma_modulo.data_inicio
                END as data
            from
                pmieducar.turma
            inner join pmieducar.ano_letivo_modulo
                on ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola
                and ano_letivo_modulo.ref_ano = turma.ano
            left join pmieducar.turma_modulo
                on turma_modulo.ref_cod_turma = turma.cod_turma
                and turma_modulo.sequencial = 1
            join pmieducar.curso on curso.cod_curso = turma.ref_cod_curso
            where true
                and ano_letivo_modulo.sequencial = 1
                and turma.cod_turma = {$cod_turma};
        ";

        $data = $this->db->UnicoCampo($query);

        if (!$data) {
            return false;
        }

        return new \DateTime($data);
    }

    public function pegaDataAnoLetivoFim($cod_turma)
    {
        $query = "
            select
                CASE WHEN curso.padrao_ano_escolar = 1
                    THEN ano_letivo_modulo.data_fim
                ELSE
                    turma_modulo.data_fim
                END as data
            from
                pmieducar.turma
            inner join (
                select
                    ano_letivo_modulo.data_fim,
                    ano_letivo_modulo.ref_ref_cod_escola,
                    ano_letivo_modulo.ref_ano
                from
                    pmieducar.ano_letivo_modulo
                inner join pmieducar.modulo
                    on modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
                where true
                    and ano_letivo_modulo.sequencial = modulo.num_etapas
            ) as ano_letivo_modulo
                on ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola
                and ano_letivo_modulo.ref_ano = turma.ano
            left join (
                select
                    turma_modulo.data_fim,
                    turma_modulo.ref_cod_turma
                from
                    pmieducar.turma_modulo
                inner join pmieducar.modulo
                    on modulo.cod_modulo = turma_modulo.ref_cod_modulo
                where true
                    and turma_modulo.sequencial = modulo.num_etapas
            ) as turma_modulo
                on turma_modulo.ref_cod_turma = turma.cod_turma
            join pmieducar.curso on curso.cod_curso = turma.ref_cod_curso
            where true
                and turma.cod_turma = {$cod_turma};
        ";

        $data = $this->db->UnicoCampo($query);

        if (!$data) {
            return false;
        }

        return new \DateTime($data);
    }
}
