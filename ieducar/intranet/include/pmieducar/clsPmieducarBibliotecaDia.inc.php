<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarBibliotecaDia extends Model
{
    public $ref_cod_biblioteca;
    public $dia;

    public function __construct($ref_cod_biblioteca = null, $dia = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}biblioteca_dia";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_biblioteca, dia';

        if (is_numeric($ref_cod_biblioteca)) {
                    $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }

        if (is_numeric($dia)) {
            $this->dia = $dia;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->dia)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->dia)) {
                $campos .= "{$gruda}dia";
                $valores .= "{$gruda}'{$this->dia}'";
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->dia)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND dia = '{$this->dia}'");

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
    public function lista($int_ref_cod_biblioteca = null, $int_dia = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_dia)) {
            $filtros .= "{$whereAnd} dia = '{$int_dia}'";
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->dia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND dia = '{$this->dia}'");
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->dia)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND dia = '{$this->dia}'");
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->dia)) {
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a uma biblioteca
     */
    public function excluirTodos()
    {
        if (is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'");

            return true;
        }

        return false;
    }
}
