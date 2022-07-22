<?php

use iEducar\Legacy\Model;

class clsPmieducarArredondamento extends Model
{
    public $ref_cod_curso;
    public $sequencial;
    public $valor;
    public $valor_min;
    public $valor_max;

    public function __construct($ref_cod_curso = null, $sequencial = null, $valor = null, $valor_min = null, $valor_max = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}arredondamento";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_curso, sequencial, valor, valor_min, valor_max';

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }

        if (is_numeric($sequencial)) {
            $this->sequencial = $sequencial;
        }
        if (is_numeric($valor)) {
            $this->valor = $valor;
        }
        if (is_numeric($valor_min)) {
            $this->valor_min = $valor_min;
        }
        if (is_numeric($valor_max)) {
            $this->valor_max = $valor_max;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_curso) && is_numeric($this->sequencial) && is_numeric($this->valor) && is_numeric($this->valor_min) && is_numeric($this->valor_max)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }
            if (is_numeric($this->sequencial)) {
                $campos .= "{$gruda}sequencial";
                $valores .= "{$gruda}'{$this->sequencial}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor)) {
                $campos .= "{$gruda}valor";
                $valores .= "{$gruda}'{$this->valor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_min)) {
                $campos .= "{$gruda}valor_min";
                $valores .= "{$gruda}'{$this->valor_min}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_max)) {
                $campos .= "{$gruda}valor_max";
                $valores .= "{$gruda}'{$this->valor_max}'";
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
        if (is_numeric($this->ref_cod_curso) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->valor)) {
                $set .= "{$gruda}valor = '{$this->valor}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_min)) {
                $set .= "{$gruda}valor_min = '{$this->valor_min}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_max)) {
                $set .= "{$gruda}valor_max = '{$this->valor_max}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_curso = '{$this->ref_cod_curso}' AND sequencial = '{$this->sequencial}'");

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
    public function lista($int_ref_cod_curso = null, $int_sequencial = null, $int_valor = null, $int_valor_min = null, $int_valor_max = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_sequencial)) {
            $filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor)) {
            $filtros .= "{$whereAnd} valor = '{$int_valor}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_min)) {
            $filtros .= "{$whereAnd} valor_min = '{$int_valor_min}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_max)) {
            $filtros .= "{$whereAnd} valor_max = '{$int_valor_max}'";
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
        if (is_numeric($this->ref_cod_curso) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_curso = '{$this->ref_cod_curso}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_curso) && is_numeric($this->sequencial)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_curso = '{$this->ref_cod_curso}' AND sequencial = '{$this->sequencial}'");
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
        if (is_numeric($this->ref_cod_curso) && is_numeric($this->sequencial)) {
        }

        return false;
    }
}
