<?php

use iEducar\Legacy\Model;

require_once 'include/urbano/geral.inc.php';

class clsUrbanoTipoLogradouro extends Model
{
    public $idtlog;
    public $descricao;

    /**
     * Construtor (PHP 4)
     *
     * @param string idtlog
     * @param string descricao
     *
     * @return object
     */
    public function __construct($idtlog = null, $descricao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'urbano.';
        $this->_tabela = "{$this->_schema}tipo_logradouro";

        $this->_campos_lista = $this->_todos_campos = 'idtlog, descricao';

        if (is_string($idtlog)) {
            $this->idtlog = $idtlog;
        }
        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->idtlog) && is_string($this->descricao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->idtlog)) {
                $campos .= "{$gruda}idtlog";
                $valores .= "{$gruda}'{$this->idtlog}'";
                $gruda = ', ';
            }
            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_idtlog_seq");
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
        if (is_string($this->idtlog)) {
            $db = new clsBanco();
            $set = '';

            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idtlog = '{$this->idtlog}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param string str_descricao
     *
     * @return array
     */
    public function lista($str_descricao = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_string($str_idtlog)) {
            $filtros .= "{$whereAnd} idtlog LIKE '%{$str_idtlog}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_descricao)) {
            $filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
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
        if (is_string($this->idtlog)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idtlog = '{$this->idtlog}'");
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
        if (is_string($this->idtlog)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE idtlog = '{$this->idtlog}'");
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
    public function excluir()
    {
        if (is_string($this->idtlog)) {
        }

        return false;
    }
}
