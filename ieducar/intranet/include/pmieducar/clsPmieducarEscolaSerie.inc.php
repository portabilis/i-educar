<?php

use iEducar\Legacy\Model;

class clsPmieducarEscolaSerie extends Model
{
    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $hora_inicial;
    public $hora_final;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;
    public $bloquear_enturmacao_sem_vagas;
    public $bloquear_cadastro_turma_para_serie_com_vagas;
    public $codUsuario;
    public $anos_letivos;

    public function __construct($ref_cod_escola = null, $ref_cod_serie = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $hora_inicial = null, $hora_final = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $hora_inicio_intervalo = null, $hora_fim_intervalo = null, $bloquear_enturmacao_sem_vagas = null, $bloquear_cadastro_turma_para_serie_com_vagas = null, $anos_letivos = [])
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}escola_serie";

        $this->_campos_lista = $this->_todos_campos = 'es.ref_cod_escola, es.ref_cod_serie, es.ref_usuario_exc, es.ref_usuario_cad, es.hora_inicial, es.hora_final, es.data_cadastro, es.data_exclusao, es.ativo, es.hora_inicio_intervalo, es.hora_fim_intervalo, es.bloquear_enturmacao_sem_vagas, es.bloquear_cadastro_turma_para_serie_com_vagas, ARRAY_TO_JSON(es.anos_letivos) AS anos_letivos ';

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_cod_serie)) {
            $this->ref_cod_serie = $ref_cod_serie;
        }
        if (is_numeric($ref_cod_escola)) {
            $this->ref_cod_escola = $ref_cod_escola;
        }

        if (($hora_inicial)) {
            $this->hora_inicial = $hora_inicial;
        }
        if (($hora_final)) {
            $this->hora_final = $hora_final;
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
        if (($hora_inicio_intervalo)) {
            $this->hora_inicio_intervalo = $hora_inicio_intervalo;
        }
        if (($hora_fim_intervalo)) {
            $this->hora_fim_intervalo = $hora_fim_intervalo;
        }
        if (is_array($anos_letivos)) {
            $this->anos_letivos = $anos_letivos;
        }

        $this->bloquear_enturmacao_sem_vagas = $bloquear_enturmacao_sem_vagas;
        $this->bloquear_cadastro_turma_para_serie_com_vagas = $bloquear_cadastro_turma_para_serie_com_vagas;
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_usuario_cad)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_escola)) {
                $campos .= "{$gruda}ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (($this->hora_inicial)) {
                $campos .= "{$gruda}hora_inicial";
                $valores .= "{$gruda}'{$this->hora_inicial}'";
                $gruda = ', ';
            }
            if (($this->hora_final)) {
                $campos .= "{$gruda}hora_final";
                $valores .= "{$gruda}'{$this->hora_final}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (($this->hora_inicio_intervalo)) {
                $campos .= "{$gruda}hora_inicio_intervalo";
                $valores .= "{$gruda}'{$this->hora_inicio_intervalo}'";
                $gruda = ', ';
            }
            if (($this->hora_fim_intervalo)) {
                $campos .= "{$gruda}hora_fim_intervalo";
                $valores .= "{$gruda}'{$this->hora_fim_intervalo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->bloquear_enturmacao_sem_vagas)) {
                $campos .= "{$gruda}bloquear_enturmacao_sem_vagas";
                $valores .= "{$gruda}'{$this->bloquear_enturmacao_sem_vagas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->bloquear_cadastro_turma_para_serie_com_vagas)) {
                $campos .= "{$gruda}bloquear_cadastro_turma_para_serie_com_vagas";
                $valores .= "{$gruda}'{$this->bloquear_cadastro_turma_para_serie_com_vagas}'";
                $gruda = ', ';
            }

            if (is_array($this->anos_letivos)) {
                $campos .= "{$gruda}anos_letivos";
                $valores .= "{$gruda} " . Portabilis_Utils_Database::arrayToPgArray($this->anos_letivos) . ' ';
                $grupo = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return true;
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_usuario_exc)) {
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
            if (($this->hora_inicial)) {
                $set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
                $gruda = ', ';
            }
            if (($this->hora_final)) {
                $set .= "{$gruda}hora_final = '{$this->hora_final}'";
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
            if (($this->hora_inicio_intervalo)) {
                $set .= "{$gruda}hora_inicio_intervalo = '{$this->hora_inicio_intervalo}'";
                $gruda = ', ';
            }
            if (($this->hora_fim_intervalo)) {
                $set .= "{$gruda}hora_fim_intervalo = '{$this->hora_fim_intervalo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->bloquear_enturmacao_sem_vagas)) {
                $set .= "{$gruda}bloquear_enturmacao_sem_vagas = '{$this->bloquear_enturmacao_sem_vagas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->bloquear_cadastro_turma_para_serie_com_vagas)) {
                $set .= "{$gruda}bloquear_cadastro_turma_para_serie_com_vagas = '{$this->bloquear_cadastro_turma_para_serie_com_vagas}'";
                $gruda = ', ';
            }
            if (is_array($this->anos_letivos)) {
                $set .= "{$gruda} anos_letivos = " . Portabilis_Utils_Database::arrayToPgArray($this->anos_letivos) . ' ';
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'");

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
    public function lista($int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_instituicao = null, $int_ref_cod_curso = null, $bloquear_enturmacao_sem_vagas = null, $bloquear_cadastro_turma_para_serie_com_vagas = null)
    {
        $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, s.ref_cod_curso, s.nm_serie FROM {$this->_tabela} es, {$this->_schema}serie s, {$this->_schema}curso c";

        $whereAnd = ' AND ';
        $filtros = ' WHERE es.ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso AND s.ativo = 1 ';

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} es.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                                                                WHERE escola_usuario.ref_cod_escola = es.ref_cod_escola
                                                                                  AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} es.ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} es.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} es.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_ini)) {
            $filtros .= "{$whereAnd} es.hora_inicial >= '{$time_hora_inicial_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_fim)) {
            $filtros .= "{$whereAnd} es.hora_inicial <= '{$time_hora_inicial_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_ini)) {
            $filtros .= "{$whereAnd} es.hora_final >= '{$time_hora_final_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_fim)) {
            $filtros .= "{$whereAnd} es.hora_final <= '{$time_hora_final_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} es.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} es.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} es.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} es.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} es.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} es.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_ini)) {
            $filtros .= "{$whereAnd} es.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_fim)) {
            $filtros .= "{$whereAnd} es.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_ini)) {
            $filtros .= "{$whereAnd} es.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_fim)) {
            $filtros .= "{$whereAnd} es.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($bloquear_enturmacao_sem_vagas)) {
            $filtros .= "{$whereAnd} s.bloquear_enturmacao_sem_vagas = '{$bloquear_enturmacao_sem_vagas}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($bloquear_cadastro_turma_para_serie_com_vagas)) {
            $filtros .= "{$whereAnd} s.bloquear_cadastro_turma_para_serie_com_vagas = '{$bloquear_cadastro_turma_para_serie_com_vagas}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} es, {$this->_schema}serie s, {$this->_schema}curso c {$filtros}");

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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} es WHERE es.ref_cod_escola = '{$this->ref_cod_escola}' AND es.ref_cod_serie = '{$this->ref_cod_serie}'");
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
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
