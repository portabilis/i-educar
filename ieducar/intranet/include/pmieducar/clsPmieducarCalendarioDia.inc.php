<?php

use iEducar\Legacy\Model;

class clsPmieducarCalendarioDia extends Model
{
    public $ref_cod_calendario_ano_letivo;
    public $mes;
    public $dia;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_calendario_dia_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function __construct($ref_cod_calendario_ano_letivo = null, $mes = null, $dia = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_calendario_dia_motivo = null/*, $ref_cod_calendario_atividade = null*/, $descricao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}calendario_dia";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_calendario_ano_letivo, mes, dia, ref_usuario_exc, ref_usuario_cad, ref_cod_calendario_dia_motivo, descricao, data_cadastro, data_exclusao, ativo';

        if (is_numeric($ref_cod_calendario_dia_motivo)) {
            $this->ref_cod_calendario_dia_motivo = $ref_cod_calendario_dia_motivo;
        } elseif ($ref_cod_calendario_dia_motivo = 'NULL') {
            $this->ref_cod_calendario_dia_motivo = $ref_cod_calendario_dia_motivo;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_calendario_ano_letivo)) {
            $this->ref_cod_calendario_ano_letivo = $ref_cod_calendario_ano_letivo;
        }

        if (is_numeric($mes)) {
            $this->mes = $mes;
        }
        if (is_numeric($dia)) {
            $this->dia = $dia;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
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
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_calendario_ano_letivo) && is_numeric($this->mes) && is_numeric($this->dia) && is_numeric($this->ref_usuario_cad) /*&& (is_numeric( $this->ref_cod_calendario_dia_motivo )|| is_numeric( $this->ref_cod_calendario_atividade ))/* && is_string( $this->descricao )*/) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_calendario_ano_letivo)) {
                $campos .= "{$gruda}ref_cod_calendario_ano_letivo";
                $valores .= "{$gruda}'{$this->ref_cod_calendario_ano_letivo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->mes)) {
                $campos .= "{$gruda}mes";
                $valores .= "{$gruda}'{$this->mes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dia)) {
                $campos .= "{$gruda}dia";
                $valores .= "{$gruda}'{$this->dia}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_calendario_dia_motivo)) {
                $campos .= "{$gruda}ref_cod_calendario_dia_motivo";
                $valores .= "{$gruda}'{$this->ref_cod_calendario_dia_motivo}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
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
        if (is_numeric($this->ref_cod_calendario_ano_letivo) && is_numeric($this->mes) && is_numeric($this->dia) && is_numeric($this->ref_usuario_exc)) {
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

            if (is_numeric($this->ref_cod_calendario_dia_motivo) || $this->ref_cod_calendario_dia_motivo == 'NULL') {
                $set .= "{$gruda}ref_cod_calendario_dia_motivo = {$this->ref_cod_calendario_dia_motivo}";
                $gruda = ', ';
            }
            if (is_string($this->descricao) && $this->descricao != 'NULL') {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            } elseif ($this->descricao == 'NULL') {
                $set .= "{$gruda}descricao = {$this->descricao}";
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
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_calendario_ano_letivo = '{$this->ref_cod_calendario_ano_letivo}' AND mes = '{$this->mes}' AND dia = '{$this->dia}'");

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
    public function lista($int_ref_cod_calendario_ano_letivo = null, $int_mes = null, $int_dia = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_calendario_dia_motivo = null/*, $int_ref_cod_calendario_atividade = null*/, $str_descricao = null, $date_descricao_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_tipo_dia_in = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} c";

        $whereAnd = ' WHERE ';

        $filtros = '';

        if (is_string($str_tipo_dia_in)) {
            $filtros .= ", pmieducar.calendario_dia_motivo m WHERE c.ref_cod_calendario_dia_motivo = m.cod_calendario_dia_motivo AND m.tipo in($str_tipo_dia_in) ";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_calendario_ano_letivo)) {
            $filtros .= "{$whereAnd} c.ref_cod_calendario_ano_letivo = '{$int_ref_cod_calendario_ano_letivo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_mes)) {
            $filtros .= "{$whereAnd} c.mes = '{$int_mes}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dia)) {
            $filtros .= "{$whereAnd} c.dia = '{$int_dia}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_calendario_dia_motivo)) {
            $filtros .= "{$whereAnd} c.ref_cod_calendario_dia_motivo = '{$int_ref_cod_calendario_dia_motivo}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} c.descricao like '%{$str_descricao}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} c.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} c.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_string($tipo_dia)) {
            $filtros .= "{$whereAnd} exists (SELECT FROM pmieducar.calendario_dia_motivo WHERE )";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} c {$filtros}");
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
        if (is_numeric($this->ref_cod_calendario_ano_letivo) && is_numeric($this->mes) && is_numeric($this->dia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_calendario_ano_letivo = '{$this->ref_cod_calendario_ano_letivo}' AND mes = '{$this->mes}' AND dia = '{$this->dia}'");
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
        if (is_numeric($this->ref_cod_calendario_ano_letivo) && is_numeric($this->mes) && is_numeric($this->dia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_calendario_ano_letivo = '{$this->ref_cod_calendario_ano_letivo}' AND mes = '{$this->mes}' AND dia = '{$this->dia}'");
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
        if (is_numeric($this->ref_cod_calendario_ano_letivo) && is_numeric($this->mes) && is_numeric($this->dia) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
