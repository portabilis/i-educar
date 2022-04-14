<?php

use iEducar\Legacy\Model;

class clsPmieducarBibliotecaUsuario extends Model
{
    public $ref_cod_biblioteca;
    public $ref_cod_usuario;

    public function __construct($ref_cod_biblioteca = null, $ref_cod_usuario = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}biblioteca_usuario";

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_biblioteca, ref_cod_usuario';

        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
        if (is_numeric($ref_cod_usuario)) {
            $this->ref_cod_usuario = $ref_cod_usuario;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_cod_usuario)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_cod_biblioteca)) {
                $campos .= "{$gruda}ref_cod_biblioteca";
                $valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_usuario)) {
                $campos .= "{$gruda}ref_cod_usuario";
                $valores .= "{$gruda}'{$this->ref_cod_usuario}'";
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_cod_usuario)) {
            $db = new clsBanco();
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND ref_cod_usuario = '{$this->ref_cod_usuario}'");

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
    public function lista($int_ref_cod_biblioteca = null, $int_ref_cod_usuario = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_usuario)) {
            $filtros .= "{$whereAnd} ref_cod_usuario = '{$int_ref_cod_usuario}'";
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

    public function listaBibliotecas($int_ref_cod_pessoa)
    {
        if (is_numeric($int_ref_cod_pessoa)) {
            $db = new clsBanco();
            $db->Consulta("SELECT ref_cod_biblioteca FROM {$this->_tabela} WHERE ref_cod_usuario = '$int_ref_cod_pessoa'");
            while ($db->ProximoRegistro()) {
                list($cod_biblioteca) = $db->Tupla();
                $retorno[] = $cod_biblioteca;
            }

            return $retorno;
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_cod_usuario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND ref_cod_usuario = '{$this->ref_cod_usuario}'");
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_cod_usuario)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND ref_cod_usuario = '{$this->ref_cod_usuario}'");
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
        if (is_numeric($this->ref_cod_biblioteca) && is_numeric($this->ref_cod_usuario)) {

            //delete
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_biblioteca = '{$this->ref_cod_biblioteca}' AND ref_cod_usuario = '{$this->ref_cod_usuario}'");

            return true;
        }

        return false;
    }

    /**
     * Exclui todos os registros referentes a um tipo de avaliacao
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
