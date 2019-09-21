<?php

use iEducar\Legacy\Model;

require_once('include/pmieducar/geral.inc.php');

class clsPmieducarQuadroHorario extends Model
{
    public $cod_quadro_horario;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_turma;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ano;

    public function __construct($cod_quadro_horario = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_turma = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ano = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}quadro_horario";

        $this->_campos_lista = $this->_todos_campos = 'cod_quadro_horario, ref_usuario_exc, ref_usuario_cad, ref_cod_turma, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_turma)) {
                    $this->ref_cod_turma = $ref_cod_turma;
        }

        if (is_numeric($cod_quadro_horario)) {
            $this->cod_quadro_horario = $cod_quadro_horario;
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
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_turma)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_turma)) {
                $campos .= "{$gruda}ref_cod_turma";
                $valores .= "{$gruda}'{$this->ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_quadro_horario_seq");
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
        if (is_numeric($this->cod_quadro_horario) && is_numeric($this->ref_usuario_exc)) {
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
            if (is_numeric($this->ref_cod_turma)) {
                $set .= "{$gruda}ref_cod_turma = '{$this->ref_cod_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
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

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_quadro_horario = '{$this->cod_quadro_horario}'");

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
    public function lista($int_cod_quadro_horario = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_turma = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_quadro_horario)) {
            $filtros .= "{$whereAnd} cod_quadro_horario = '{$int_cod_quadro_horario}'";
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
        if (is_numeric($int_ref_cod_turma)) {
            $filtros .= "{$whereAnd} ref_cod_turma = '{$int_ref_cod_turma}'";
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
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
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
        if (is_numeric($this->cod_quadro_horario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_quadro_horario = '{$this->cod_quadro_horario}' AND ativo = 1");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->ref_cod_turma)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_turma = '{$this->ref_cod_turma}' AND ativo = 1");
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
        if (is_numeric($this->cod_quadro_horario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_quadro_horario = '{$this->cod_quadro_horario}'");
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
        if (is_numeric($this->cod_quadro_horario) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
