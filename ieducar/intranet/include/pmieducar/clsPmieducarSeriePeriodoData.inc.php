<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarSeriePeriodoData extends Model
{
    public $ref_cod_serie;
    public $sequencial;
    public $ref_cod_serie_tipo_periodo_ano;
    public $data_inicial;
    public $data_final;

    public function __construct($ref_cod_serie = null, $sequencial = null, $ref_cod_serie_tipo_periodo_ano = null, $data_inicial = null, $data_final = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}serie_periodo_data";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_serie, sequencial, ref_cod_serie_tipo_periodo_ano, data_inicial, data_final';

        if (is_numeric($ref_cod_serie_tipo_periodo_ano)) {
                    $this->ref_cod_serie_tipo_periodo_ano = $ref_cod_serie_tipo_periodo_ano;
        }
        if (is_numeric($ref_cod_serie)) {
                    $this->ref_cod_serie = $ref_cod_serie;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_string($data_inicial)) {
            $this->data_inicial = $data_inicial;
        }
        if (is_string($data_final)) {
            $this->data_final = $data_final;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_serie) && is_numeric($this->sequencial) && is_numeric($this->ref_cod_serie_tipo_periodo_ano) && is_string($this->data_inicial) && is_string($this->data_final)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_serie)) {
                $campos .= "{$gruda}ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_serie_tipo_periodo_ano)) {
                $campos .= "{$gruda}ref_cod_serie_tipo_periodo_ano";
                $valores .= "{$gruda}'{$this->ref_cod_serie_tipo_periodo_ano}'";
                $gruda = ', ';
            }
            if (is_string($this->data_inicial)) {
                $campos .= "{$gruda}data_inicial";
                $valores .= "{$gruda}'{$this->data_inicial}'";
                $gruda = ', ';
            }
            if (is_string($this->data_final)) {
                $campos .= "{$gruda}data_final";
                $valores .= "{$gruda}'{$this->data_final}'";
                $gruda = ', ';
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
        if (is_numeric($this->ref_cod_serie) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_serie_tipo_periodo_ano)) {
                $set .= "{$gruda}ref_cod_serie_tipo_periodo_ano = '{$this->ref_cod_serie_tipo_periodo_ano}'";
                $gruda = ', ';
            }
            if (is_string($this->data_inicial)) {
                $set .= "{$gruda}data_inicial = '{$this->data_inicial}'";
                $gruda = ', ';
            }
            if (is_string($this->data_final)) {
                $set .= "{$gruda}data_final = '{$this->data_final}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'");

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
    public function lista($int_ref_cod_serie = null, $int_sequencial = null, $int_ref_cod_serie_tipo_periodo_ano = null, $date_data_inicial_ini = null, $date_data_inicial_fim = null, $date_data_final_ini = null, $date_data_final_fim = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_serie)) {
            $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_serie_tipo_periodo_ano)) {
            $filtros .= "{$whereAnd} ref_cod_serie_tipo_periodo_ano = '{$int_ref_cod_serie_tipo_periodo_ano}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_inicial_ini)) {
            $filtros .= "{$whereAnd} data_inicial >= '{$date_data_inicial_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_inicial_fim)) {
            $filtros .= "{$whereAnd} data_inicial <= '{$date_data_inicial_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_final_ini)) {
            $filtros .= "{$whereAnd} data_final >= '{$date_data_final_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_final_fim)) {
            $filtros .= "{$whereAnd} data_final <= '{$date_data_final_fim}'";
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
        if (is_numeric($this->ref_cod_serie) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_serie) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_serie) && is_numeric($this->sequencial)) {
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_serie)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}'");

            return true;
        }

        return false;
    }
}
