<?php

use iEducar\Legacy\Model;

class clsPmieducarCalendarioDiaAnotacao extends Model
{
    public $ref_dia;
    public $ref_mes;
    public $ref_ref_cod_calendario_ano_letivo;
    public $ref_cod_calendario_anotacao;

    public function __construct($ref_dia = null, $ref_mes = null, $ref_ref_cod_calendario_ano_letivo = null, $ref_cod_calendario_anotacao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}calendario_dia_anotacao";

        $this->_campos_lista = $this->_todos_campos = 'ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao';

        if (is_numeric($ref_cod_calendario_anotacao)) {
            $this->ref_cod_calendario_anotacao = $ref_cod_calendario_anotacao;
        }
        if (is_numeric($ref_ref_cod_calendario_ano_letivo) && is_numeric($ref_mes) && is_numeric($ref_dia)) {
            $this->ref_ref_cod_calendario_ano_letivo = $ref_ref_cod_calendario_ano_letivo;
            $this->ref_mes = $ref_mes;
            $this->ref_dia = $ref_dia;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_dia) && is_numeric($this->ref_mes) && is_numeric($this->ref_ref_cod_calendario_ano_letivo) && is_numeric($this->ref_cod_calendario_anotacao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_dia)) {
                $campos .= "{$gruda}ref_dia";
                $valores .= "{$gruda}'{$this->ref_dia}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_mes)) {
                $campos .= "{$gruda}ref_mes";
                $valores .= "{$gruda}'{$this->ref_mes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_calendario_ano_letivo)) {
                $campos .= "{$gruda}ref_ref_cod_calendario_ano_letivo";
                $valores .= "{$gruda}'{$this->ref_ref_cod_calendario_ano_letivo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_calendario_anotacao)) {
                $campos .= "{$gruda}ref_cod_calendario_anotacao";
                $valores .= "{$gruda}'{$this->ref_cod_calendario_anotacao}'";
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
        if (is_numeric($this->ref_dia) && is_numeric($this->ref_mes) && is_numeric($this->ref_ref_cod_calendario_ano_letivo) && is_numeric($this->ref_cod_calendario_anotacao)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'");

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
    public function lista($int_ref_dia = null, $int_ref_mes = null, $int_ref_ref_cod_calendario_ano_letivo = null, $int_ref_cod_calendario_anotacao = null, $is_ativo = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_dia)) {
            $filtros .= "{$whereAnd} ref_dia = '{$int_ref_dia}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_mes)) {
            $filtros .= "{$whereAnd} ref_mes = '{$int_ref_mes}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_calendario_ano_letivo)) {
            $filtros .= "{$whereAnd} ref_ref_cod_calendario_ano_letivo = '{$int_ref_ref_cod_calendario_ano_letivo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_calendario_anotacao)) {
            $filtros .= "{$whereAnd} ref_cod_calendario_anotacao = '{$int_ref_cod_calendario_anotacao}'";
            $whereAnd = ' AND ';
        }
        if ($is_ativo) {
            $filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.calendario_anotacao WHERE calendario_anotacao.cod_calendario_anotacao = ref_cod_calendario_anotacao and ativo = 1)";
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
        if (is_numeric($this->ref_dia) && is_numeric($this->ref_mes) && is_numeric($this->ref_ref_cod_calendario_ano_letivo) && is_numeric($this->ref_cod_calendario_anotacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'");
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
        if (is_numeric($this->ref_dia) && is_numeric($this->ref_mes) && is_numeric($this->ref_ref_cod_calendario_ano_letivo) && is_numeric($this->ref_cod_calendario_anotacao)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'");
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
        if (is_numeric($this->ref_dia) && is_numeric($this->ref_mes) && is_numeric($this->ref_ref_cod_calendario_ano_letivo) && is_numeric($this->ref_cod_calendario_anotacao)) {
        }

        return false;
    }
}
