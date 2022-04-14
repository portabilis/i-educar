<?php

use App\Models\LegacyRegistration;
use App\Services\GlobalAverageService;
use iEducar\Legacy\Model;

class clsPmieducarHistoricoEscolar extends Model
{
    public $ref_cod_aluno;
    public $sequencial;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ano;
    public $carga_horaria;
    public $dias_letivos;
    public $ref_cod_escola;
    public $escola;
    public $escola_cidade;
    public $escola_uf;
    public $observacao;
    public $aprovado;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $faltas_globalizadas;
    public $frequencia;
    public $dependencia;
    public $posicao;
    public $ref_cod_instituicao;
    public $nm_serie;
    public $origem;
    public $extra_curricular;
    public $ref_cod_matricula;

    public function __construct($ref_cod_aluno = null, $sequencial = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_serie = null, $ano = null, $carga_horaria = null, $dias_letivos = null, $escola = null, $escola_cidade = null, $escola_uf = null, $observacao = null, $aprovado = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $faltas_globalizadas = null, $ref_cod_instituicao = null, $origem = null, $extra_curricular = null, $ref_cod_matricula = null, $frequencia = null, $registro = null, $livro = null, $folha = null, $nm_curso = null, $historico_grade_curso_id = null, $aceleracao = null, $ref_cod_escola = null, $dependencia = false, $posicao = null)
    {
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}historico_escolar";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_aluno, sequencial, ref_usuario_exc, ref_usuario_cad, ano, carga_horaria, dias_letivos, escola, escola_cidade, escola_uf, observacao, aprovado, data_cadastro, data_exclusao, ativo, faltas_globalizadas, ref_cod_instituicao, nm_serie, origem, extra_curricular, ref_cod_matricula, frequencia, registro, livro, folha, nm_curso, historico_grade_curso_id, aceleracao, ref_cod_escola, dependencia, posicao';

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_aluno)) {
            $this->ref_cod_aluno = $ref_cod_aluno;
        }
        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if (is_numeric($ref_cod_matricula)) {
            $this->ref_cod_matricula = $ref_cod_matricula;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_numeric($ano)) {
            $this->ano = $ano;
        }
        if (is_numeric($carga_horaria)) {
            $this->carga_horaria = $carga_horaria;
        }
        if (is_numeric($dias_letivos)) {
            $this->dias_letivos = $dias_letivos;
        }
        if (is_numeric($posicao)) {
            $this->posicao = $posicao;
        }
        if (is_string($escola)) {
            $this->escola = $escola;
        }
        if (is_string($escola_cidade)) {
            $this->escola_cidade = $escola_cidade;
        }
        if (is_string($escola_uf)) {
            $this->escola_uf = $escola_uf;
        }
        if (is_string($observacao)) {
            $this->observacao = $observacao;
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
        if (is_numeric($faltas_globalizadas) || $faltas_globalizadas == 'NULL') {
            $this->faltas_globalizadas = $faltas_globalizadas;
        }
        if (is_numeric($origem)) {
            $this->origem = $origem;
        }
        if (is_numeric($extra_curricular)) {
            $this->extra_curricular = $extra_curricular;
        }
        if (is_string($nm_serie)) {
            $this->nm_serie = $nm_serie;
        }
        if (is_numeric($frequencia)) {
            $this->frequencia = $frequencia;
        }

        $this->registro = $registro;
        $this->livro = $livro;
        $this->folha = $folha;
        $this->nm_curso = $nm_curso;
        $this->historico_grade_curso_id = $historico_grade_curso_id;
        $this->aceleracao = $aceleracao;

        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
            $db = new clsBanco();
            $resultado = [];
            $db->Consulta("SELECT COALESCE((SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
                                      FROM cadastro.pessoa ps, cadastro.juridica
                                     WHERE escola.ref_idpes = juridica.idpes
                                       AND juridica.idpes = ps.idpes
                                       AND ps.idpes = escola.ref_idpes),
                                   (SELECT nm_escola
                                      FROM pmieducar.escola_complemento
                                    WHERE ref_cod_escola = escola.cod_escola)) AS nome
                             FROM pmieducar.escola
                            WHERE escola.cod_escola = $this->ref_cod_escola
                         ORDER BY nome");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->escola = $tupla['nome'];
            }
        } else {
            $this->ref_cod_escola = null;
        }
        if (dbBool($dependencia)) {
            $this->dependencia = $dependencia;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ref_usuario_cad) && is_string($this->nm_serie) && is_numeric($this->ano) && is_string($this->escola) && is_string($this->escola_cidade) && is_numeric($this->aprovado) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_aluno)) {
                $campos .= "{$gruda}ref_cod_aluno";
                $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_serie)) {
                $serie = $db->escapeString($this->nm_serie);
                $campos .= "{$gruda}nm_serie";
                $valores .= "{$gruda}'{$serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->origem)) {
                $campos .= "{$gruda}origem";
                $valores .= "{$gruda}'{$this->origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->extra_curricular)) {
                $campos .= "{$gruda}extra_curricular";
                $valores .= "{$gruda}'{$this->extra_curricular}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula)) {
                $campos .= "{$gruda}ref_cod_matricula";
                $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }
            if (is_numeric($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ', ';
            } elseif (is_null($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            if (is_numeric($this->dias_letivos)) {
                $campos .= "{$gruda}dias_letivos";
                $valores .= "{$gruda}'{$this->dias_letivos}'";
                $gruda = ', ';
            } elseif (is_null($this->dias_letivos)) {
                $campos .= "{$gruda}dias_letivos";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }
            if (is_string($this->escola)) {
                $escola = $db->escapeString($this->escola);
                $campos .= "{$gruda}escola";
                $valores .= "{$gruda}E'{$escola}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_cidade)) {
                $escola_cidade = $db->escapeString($this->escola_cidade);
                $campos .= "{$gruda}escola_cidade";
                $valores .= "{$gruda}E'{$escola_cidade}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_uf)) {
                $escola_uf = $db->escapeString($this->escola_uf);
                $campos .= "{$gruda}escola_uf";
                $valores .= "{$gruda}'{$escola_uf}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}E'{$observacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->aprovado)) {
                $campos .= "{$gruda}aprovado";
                $valores .= "{$gruda}'{$this->aprovado}'";
                $gruda = ', ';
            }
            if (is_numeric($this->faltas_globalizadas)) {
                $campos .= "{$gruda}faltas_globalizadas";
                $valores .= "{$gruda}'{$this->faltas_globalizadas}'";
                $gruda = ', ';
            }
            if (is_numeric($this->frequencia)) {
                $campos .= "{$gruda}frequencia";
                $valores .= "{$gruda}'{$this->frequencia}'";
                $gruda = ', ';
            } elseif (is_null($this->frequencia)) {
                $campos .= "{$gruda}frequencia";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }
            if (is_string($this->registro)) {
                $registro = $db->escapeString($this->registro);
                $campos .= "{$gruda}registro";
                $valores .= "{$gruda}E'{$registro}'";
                $gruda = ', ';
            }
            if (is_string($this->livro)) {
                $livro = $db->escapeString($this->livro);
                $campos .= "{$gruda}livro";
                $valores .= "{$gruda}E'{$livro}'";
                $gruda = ', ';
            }
            if (is_string($this->folha)) {
                $folha = $db->escapeString($this->folha);
                $campos .= "{$gruda}folha";
                $valores .= "{$gruda}E'{$folha}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_curso)) {
                $nm_curso = $db->escapeString($this->nm_curso);
                $campos .= "{$gruda}nm_curso";
                $valores .= "{$gruda}E'{$nm_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->historico_grade_curso_id)) {
                $campos .= "{$gruda}historico_grade_curso_id";
                $valores .= "{$gruda}'{$this->historico_grade_curso_id}'";
                $gruda = ', ';
            }

            if (is_numeric($aceleracao)) {
                $campos .= "{$gruda}aceleracao";
                $valores .= "{$gruda}'{$this->aceleracao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }

            if (dbBool($this->dependencia)) {
                $campos .= "{$gruda}dependencia";
                $valores .= "{$gruda} true ";
                $gruda = ', ';
            } else {
                $campos .= "{$gruda}dependencia";
                $valores .= "{$gruda} false ";
                $gruda = ', ';
            }

            if (is_numeric($this->posicao)) {
                $campos .= "{$gruda}posicao";
                $valores .= "{$gruda}'{$this->posicao}'";
                $gruda = ', ';
            } elseif (is_null($this->posicao)) {
                $campos .= "{$gruda}posicao";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $this->sequencial = $db->campoUnico("SELECT COALESCE( MAX(sequencial), 0 ) + 1 FROM {$this->_tabela} WHERE ref_cod_aluno = {$this->ref_cod_aluno}");

            $db->Consulta("INSERT INTO {$this->_tabela} ( sequencial, $campos ) VALUES( $this->sequencial, $valores )");

            if ($this->ref_cod_aluno) {
                $detalhe = $this->detalhe();
            }

            return $this->sequencial;
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_serie)) {
                $nm_serie = $db->escapeString($this->nm_serie);
                $set .= "{$gruda}nm_serie = '{$nm_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->origem)) {
                $set .= "{$gruda}origem = '{$this->origem}'";
                $gruda = ', ';
            }
            if (is_numeric($this->extra_curricular)) {
                $set .= "{$gruda}extra_curricular = '{$this->extra_curricular}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_matricula)) {
                $set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }
            if (is_numeric($this->carga_horaria)) {
                $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
                $gruda = ', ';
            } elseif (is_null($this->carga_horaria)) {
                $set .= "{$gruda}carga_horaria = NULL";
                $gruda = ', ';
            }
            if (is_numeric($this->dias_letivos)) {
                $set .= "{$gruda}dias_letivos = '{$this->dias_letivos}'";
                $gruda = ', ';
            } elseif (is_null($this->dias_letivos)) {
                $set .= "{$gruda}dias_letivos = NULL";
                $gruda = ', ';
            }
            if (is_string($this->escola)) {
                $escola = $db->escapeString($this->escola);
                $set .= "{$gruda}escola = E'{$escola}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_cidade)) {
                $escola_cidade = $db->escapeString($this->escola_cidade);
                $set .= "{$gruda}escola_cidade = E'{$escola_cidade}'";
                $gruda = ', ';
            }
            if (is_string($this->escola_uf)) {
                $escola_uf = $db->escapeString($this->escola_uf);
                $set .= "{$gruda}escola_uf = '{$escola_uf}'";
                $gruda = ', ';
            }
            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = E'{$observacao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->aprovado)) {
                $set .= "{$gruda}aprovado = '{$this->aprovado}'";
                $gruda = ', ';
            }
            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->frequencia)) {
                $set .= "{$gruda}frequencia = '{$this->frequencia}'";
                $gruda = ', ';
            } elseif (is_null($this->frequencia)) {
                $set .= "{$gruda}frequencia = NULL";
                $gruda = ', ';
            }
            if (is_numeric($this->faltas_globalizadas)) {
                $set .= "{$gruda}faltas_globalizadas = '{$this->faltas_globalizadas}'";
                $gruda = ', ';
            } elseif ($this->faltas_globalizadas == 'NULL') {
                $set .= "{$gruda}faltas_globalizadas = NULL";
                $gruda = ', ';
            }
            if (is_string($this->registro)) {
                $registro = $db->escapeString($this->registro);
                $set .= "{$gruda}registro = E'{$registro}'";
                $gruda = ', ';
            }
            if (is_string($this->livro)) {
                $livro = $db->escapeString($this->livro);
                $set .= "{$gruda}livro = E'{$livro}'";
                $gruda = ', ';
            }
            if (is_string($this->folha)) {
                $folha = $db->escapeString($this->folha);
                $set .= "{$gruda}folha = E'{$folha}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_curso)) {
                $nm_curso = $db->escapeString($this->nm_curso);
                $set .= "{$gruda}nm_curso = E'{$nm_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->historico_grade_curso_id)) {
                $set .= "{$gruda}historico_grade_curso_id = '{$this->historico_grade_curso_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->aceleracao)) {
                $set .= "{$gruda}aceleracao = '{$this->aceleracao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_cod_escola = NULL";
                $gruda = ', ';
            }

            if (dbBool($this->dependencia)) {
                $set .= "{$gruda}dependencia = true ";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}dependencia = false ";
                $gruda = ', ';
            }

            if (is_numeric($this->posicao)) {
                $set .= "{$gruda}posicao = '{$this->posicao}'";
                $gruda = ', ';
            } elseif (is_null($this->posicao)) {
                $set .= "{$gruda}posicao = NULL";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_ref_cod_aluno = null, $int_sequencial = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_serie = null, $int_ano = null, $int_carga_horaria = null, $int_dias_letivos = null, $str_escola = null, $str_escola_cidade = null, $str_escola_uf = null, $str_observacao = null, $int_aprovado = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_faltas_globalizadas = null, $int_ref_cod_instituicao = null, $int_origem = null, $int_extra_curricular = null, $int_ref_cod_matricula = null, $int_frequencia = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_aluno)) {
            $filtros .= "{$whereAnd} ref_cod_aluno = '{$int_ref_cod_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_serie)) {
            $filtros .= "{$whereAnd} nm_serie = '{$str_nm_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_origem)) {
            $filtros .= "{$whereAnd} origem = '{$int_origem}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_extra_curricular)) {
            $filtros .= "{$whereAnd} extra_curricular = '{$int_extra_curricular}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_matricula)) {
            $filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ano)) {
            $filtros .= "{$whereAnd} ano = '{$int_ano}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_carga_horaria)) {
            $filtros .= "{$whereAnd} carga_horaria = '{$int_carga_horaria}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dias_letivos)) {
            $filtros .= "{$whereAnd} dias_letivos = '{$int_dias_letivos}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_escola)) {
            $filtros .= "{$whereAnd} escola LIKE '%{$str_escola}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_escola_cidade)) {
            $filtros .= "{$whereAnd} escola_cidade LIKE '%{$str_escola_cidade}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_escola_uf)) {
            $filtros .= "{$whereAnd} escola_uf LIKE '%{$str_escola_uf}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_observacao)) {
            $filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_aprovado)) {
            $filtros .= "{$whereAnd} aprovado = '{$int_aprovado}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (!is_null($int_ativo)) {
            if ( /*is_null( $int_ativo ) ||*/ $int_ativo) {
                $filtros .= "{$whereAnd} ativo = '1'";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} ativo = '0'";
                $whereAnd = ' AND ';
            }
        }
        if (is_numeric($int_faltas_globalizadas)) {
            $filtros .= "{$whereAnd} faltas_globalizadas = '{$int_faltas_globalizadas}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_frequencia)) {
            $filtros .= "{$whereAnd} frequencia = '{$int_frequencia}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}' AND sequencial = '{$this->sequencial}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function getCodNomeEscola()
    {
        $db = new clsBanco();
        $db->Consulta("SELECT escola, ref_cod_escola
                         FROM pmieducar.historico_escolar
                        WHERE ref_cod_aluno = $this->ref_cod_aluno
                          AND sequencial = $this->sequencial");

        if ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            return $tupla['escola'] . '-' . $tupla['ref_cod_escola'];
        }
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    public function getMaxSequencial($ref_cod_aluno)
    {
        if (is_numeric($ref_cod_aluno)) {
            $db = new clsBanco();
            $sequencial = $db->campoUnico("SELECT COALESCE( MAX(sequencial), 0 ) FROM pmieducar.historico_escolar WHERE ref_cod_aluno = {$ref_cod_aluno}");

            return $sequencial;
        }

        return false;
    }

    public static function gerarHistoricoTransferencia($ref_cod_matricula, $pessoa_logada)
    {
        $detMatricula = self::dadosMatricula($ref_cod_matricula);

        if (self::deveGerarHistorico($detMatricula['ref_cod_instituicao'])) {
            $dadosEscola = self::dadosEscola($detMatricula['ref_ref_cod_escola'], $detMatricula['ref_cod_instituicao']);

            $grade_curso_id = str_contains($detMatricula['nome_curso'], '8') ? 1 : 2;

            $historicoEscolar = new clsPmieducarHistoricoEscolar(
                $detMatricula['ref_cod_aluno'],
                null,
                null,
                $pessoa_logada,
                $detMatricula['nome_serie'],
                $detMatricula['ano'],
                $detMatricula['carga_horaria'],
                null,
                mb_strtoupper($dadosEscola['nome']),
                mb_strtoupper($dadosEscola['cidade']),
                $dadosEscola['uf'],
                '',
                4,
                date('Y-m-d'),
                null,
                1,
                null,
                $detMatricula['ref_cod_instituicao'],
                '',
                null,
                $ref_cod_matricula,
                '',
                '',
                '',
                '',
                $detMatricula['nome_curso'],
                $grade_curso_id,
                null,
                $detMatricula['ref_ref_cod_escola'],
            );

            if ($historicoEscolar->cadastra()) {
                $sequencial = (new clsPmieducarHistoricoEscolar())->getMaxSequencial($detMatricula['ref_cod_aluno']);
                $disciplinas = self::dadosDisciplinas($ref_cod_matricula);
                foreach ($disciplinas as $index => $disciplina) {
                    $historicoDisciplina = new clsPmieducarHistoricoDisciplinas(($index + 1), $detMatricula['ref_cod_aluno'], $sequencial, $disciplina, '');
                    $historicoDisciplina->cadastra();
                }
            }
        }

        return false;
    }

    protected static function dadosDisciplinas($ref_cod_matricula)
    {
        $detMatricula = self::dadosMatricula($ref_cod_matricula);
        $disciplinas = [];

        $cod_serie = $detMatricula['cod_serie'];
        $cod_escola = $detMatricula['ref_ref_cod_escola'];

        $sql = "SELECT translate(upper(cc.nome),'áéíóúýàèìòùãõâêîôûäëïöüÿçÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','AEIOUYAEIOUAOAEIOUAEIOUYCAEIOUYAEIOUAOAEIOUAEIOUC')
                  FROM pmieducar.escola_serie_disciplina esd
                 INNER JOIN modules.componente_curricular cc ON(esd.ref_cod_disciplina = cc.id)
                 WHERE esd.ref_ref_cod_serie = {$cod_serie}
                   AND esd.ref_ref_cod_escola = {$cod_escola}";
        $db = new clsBanco();
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            list($disciplinas[]) = $db->Tupla();
        }

        return $disciplinas;
    }

    protected static function dadosMatricula($ref_cod_matricula)
    {
        $sql = "SELECT m.ref_cod_aluno, nm_serie as nome_serie, s.cod_serie, m.ano, m.ref_ref_cod_escola, c.ref_cod_instituicao, c.nm_curso as nome_curso, s.carga_horaria
            FROM pmieducar.matricula m
            INNER JOIN pmieducar.serie s ON m.ref_ref_cod_serie = s.cod_serie
            INNER JOIN pmieducar.curso c ON m.ref_cod_curso = c.cod_curso
            WHERE m.cod_matricula = {$ref_cod_matricula}";
        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    protected static function dadosEscola($cod_escola, $cod_instituicao)
    {
        $sql = "select

            (select pes.nome from pmieducar.escola esc, cadastro.pessoa pes
            where esc.ref_cod_instituicao = {$cod_instituicao} and esc.cod_escola = {$cod_escola}
            and pes.idpes = esc.ref_idpes) as nome,

            (select municipio.nome from public.municipio,
            cadastro.endereco_pessoa, cadastro.juridica, public.bairro, pmieducar.escola
            where endereco_pessoa.idbai = bairro.idbai and bairro.idmun = municipio.idmun and
            juridica.idpes = endereco_pessoa.idpes and juridica.idpes = escola.ref_idpes and
            escola.cod_escola = {$cod_escola}
            ) as cidade,

            (select municipio.sigla_uf from public.municipio,
            cadastro.endereco_pessoa, cadastro.juridica, public.bairro, pmieducar.escola
            where endereco_pessoa.idbai = bairro.idbai and bairro.idmun = municipio.idmun and
            juridica.idpes = endereco_pessoa.idpes and juridica.idpes = escola.ref_idpes and
            escola.cod_escola = {$cod_escola}) as uf";
        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();

        return $db->Tupla();
    }

    protected static function deveGerarHistorico($cod_instituicao)
    {
        $db = new clsBanco();

        return dbBool($db->campoUnico("select gerar_historico_transferencia FROM pmieducar.instituicao WHERE cod_instituicao = {$cod_instituicao};"));
    }

    public function insereComponenteMediaGeral($sequencial)
    {
        if ($this->sequencial && $this->ref_cod_aluno) {
            $detalhes = $this->detalhe();

            $registration = LegacyRegistration::findOrFail($detalhes['ref_cod_matricula']);
            $service = new GlobalAverageService();
            $average = $service->getGlobalAverage($registration);
            $mediaGeral = $this->arredondaNota($registration->cod_matricula, $average, 2);
            $mediaGeral = number_format($mediaGeral, 1, '.', ',');

            $sql = "INSERT INTO pmieducar.historico_disciplinas (sequencial, ref_ref_cod_aluno, ref_sequencial, nm_disciplina, nota) values ({$sequencial}, {$this->ref_cod_aluno}, {$this->sequencial}, 'Média Geral' , {$mediaGeral});";

            $db = new clsBanco();
            $db->Consulta($sql);

            return true;
        } else {
            return null;
        }
    }

    private function arredondaNota($codMatricula, $nota, $tipoNota)
    {
        $regraAvaliacao = App_Model_IedFinder::getRegraAvaliacaoPorMatricula(
            $codMatricula
        );

        return $regraAvaliacao->tabelaArredondamento->round($nota, $tipoNota);
    }
}
