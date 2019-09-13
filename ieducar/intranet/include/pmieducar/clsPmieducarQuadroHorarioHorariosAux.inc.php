<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarQuadroHorarioHorariosAux extends Model
{
    public $ref_cod_quadro_horario;
    public $sequencial;
    public $ref_cod_disciplina;
    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_cod_instituicao_servidor;
    public $ref_servidor;
    public $dia_semana;
    public $hora_inicial;
    public $hora_final;
    public $identificador;
    public $data_cadastro;

    public function __construct($ref_cod_quadro_horario = null, $sequencial = null, $ref_cod_disciplina = null, $ref_cod_escola = null, $ref_cod_serie = null, $ref_cod_instituicao_servidor = null, $ref_servidor = null, $dia_semana = null, $hora_inicial = null, $hora_final = null, $identificador = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}quadro_horario_horarios_aux";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_quadro_horario, sequencial, ref_cod_disciplina, ref_cod_escola, ref_cod_serie, ref_cod_instituicao_servidor, ref_servidor, dia_semana, hora_inicial, hora_final, identificador, data_cadastro';

        if (is_numeric($ref_servidor) && is_numeric($ref_cod_instituicao_servidor)) {
                    $this->ref_servidor = $ref_servidor;
                    $this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
        }
        if (is_numeric($ref_cod_serie) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_disciplina)) {
                    $this->ref_cod_serie = $ref_cod_serie;
                    $this->ref_cod_escola = $ref_cod_escola;
                    $this->ref_cod_disciplina = $ref_cod_disciplina;
        }
        if (is_numeric($ref_cod_quadro_horario)) {
                    $this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_numeric($dia_semana)) {
            $this->dia_semana = $dia_semana;
        }
        if (($hora_inicial)) {
            $this->hora_inicial = $hora_inicial;
        }
        if (($hora_final)) {
            $this->hora_final = $hora_final;
        }
        if (is_string($identificador)) {
            $this->identificador = $identificador;
        }

        $this->excluirRegistrosAntigos();
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_quadro_horario) && is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_instituicao_servidor) && is_numeric($this->ref_servidor) && is_numeric($this->dia_semana) && ($this->hora_inicial) && ($this->hora_final)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_quadro_horario)) {
                $campos .= "{$gruda}ref_cod_quadro_horario";
                $valores .= "{$gruda}'{$this->ref_cod_quadro_horario}'";
                $gruda = ', ';
            }
            $this->sequencial = $db->CampoUnico("SELECT ( COALESCE( MAX( sequencial ), 0 ) + 1 ) AS sequencial
                                                    FROM pmieducar.quadro_horario_horarios_aux
                                                   WHERE ref_cod_quadro_horario = {$this->ref_cod_quadro_horario}
                                                     AND ref_cod_serie      = {$this->ref_cod_serie}
                                                     AND ref_cod_escola     = {$this->ref_cod_escola}");

            if (is_numeric($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_disciplina)) {
                $campos .= "{$gruda}ref_cod_disciplina";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
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
            if (is_numeric($this->ref_cod_instituicao_servidor)) {
                $campos .= "{$gruda}ref_cod_instituicao_servidor";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_servidor)) {
                $campos .= "{$gruda}ref_servidor";
                $valores .= "{$gruda}'{$this->ref_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dia_semana)) {
                $campos .= "{$gruda}dia_semana";
                $valores .= "{$gruda}'{$this->dia_semana}'";
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
            if (is_string($this->identificador)) {
                $campos .= "{$gruda}identificador";
                $valores .= "{$gruda}'{$this->identificador}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

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
        if (is_numeric($this->ref_cod_quadro_horario) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_disciplina)) {
                $set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_escola)) {
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie)) {
                $set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao_servidor)) {
                $set .= "{$gruda}ref_cod_instituicao_servidor = '{$this->ref_cod_instituicao_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_servidor)) {
                $set .= "{$gruda}ref_servidor = '{$this->ref_servidor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dia_semana)) {
                $set .= "{$gruda}dia_semana = '{$this->dia_semana}'";
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
            if (is_string($this->identificador)) {
                $set .= "{$gruda}identificador = '{$this->identificador}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param integer int_ref_cod_disciplina
     * @param integer int_ref_cod_escola
     * @param integer int_ref_cod_serie
     * @param integer int_ref_cod_instituicao_servidor
     * @param integer int_ref_servidor
     * @param integer int_dia_semana
     * @param string time_hora_inicial_ini
     * @param string time_hora_inicial_fim
     * @param string time_hora_final_ini
     * @param string time_hora_final_fim
     * @param string str_identificador
     *
     * @return array
     */
    public function lista($int_ref_cod_disciplina = null, $int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_cod_instituicao_servidor = null, $int_ref_servidor = null, $int_dia_semana = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $str_identificador = null, $str_data_cadastro_ini = null, $str_data_cadastro_fim = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_quadro_horario)) {
            $filtros .= "{$whereAnd} ref_cod_quadro_horario = '{$int_ref_cod_quadro_horario}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_servidor)) {
            $filtros .= "{$whereAnd} ref_servidor = '{$int_ref_servidor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dia_semana)) {
            $filtros .= "{$whereAnd} dia_semana = '{$int_dia_semana}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_ini)) {
            $filtros .= "{$whereAnd} hora_inicial >= '{$time_hora_inicial_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_fim)) {
            $filtros .= "{$whereAnd} hora_inicial <= '{$time_hora_inicial_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_ini)) {
            $filtros .= "{$whereAnd} hora_final >= '{$time_hora_final_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_fim)) {
            $filtros .= "{$whereAnd} hora_final <= '{$time_hora_final_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_identificador)) {
            $filtros .= "{$whereAnd} identificador LIKE '%{$str_identificador}%'";
            $whereAnd = ' AND ';
        }

        if (($str_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (($str_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
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
        if (is_numeric($this->ref_cod_quadro_horario) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna true se o registro existir. Caso contrário retorna false.
     *
     * @return bool
     */
    public function existe()
    {
        if (is_numeric($this->ref_cod_quadro_horario) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'");
            if ($db->ProximoRegistro()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluiRegistro($ref_cod_quadro_horario, $ref_cod_serie, $ref_cod_escola, $ref_cod_disciplina, $ref_cod_instituicao_servidor, $ref_servidor, $identificador)
    {
        if (is_numeric($ref_cod_quadro_horario) && is_numeric($ref_cod_serie) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_disciplina) && is_numeric($ref_cod_instituicao_servidor) && is_numeric($ref_servidor) && is_numeric($identificador)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$ref_cod_quadro_horario}' AND ref_cod_serie = '$ref_cod_serie' AND ref_cod_escola = '$ref_cod_escola' AND ref_cod_disciplina = '$ref_cod_disciplina' AND ref_cod_instituicao_servidor = '$ref_cod_instituicao_servidor' AND ref_servidor = '$ref_servidor' AND identificador = '$identificador'");

            return true;
        }

        return false;
    }

    /**
     * Exclui todos registros de um identificador
     *
     * @return bool
     */
    public function excluirTodos($identificador)
    {
        if (is_numeric($identificador)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE identificador = '{$identificador}'");

            return true;
        }

        return false;
    }

    /**
     * Exclui todos registros de um identificador
     *
     * @return bool
     */
    public function excluirRegistrosAntigos()
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM {$this->_tabela} WHERE data_cadastro < NOW() - interval '3 hours'");

        return true;
    }
}
