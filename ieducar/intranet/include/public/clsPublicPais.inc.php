<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicPais extends Model
{
    public $idpais;
    public $nome;
    public $geom;
    public $cod_ibge;

    /**
     * Construtor (PHP 4)
     *
     * @param integer idpais
     * @param string nome
     * @param string geom
     *
     * @return object
     */
    public function __construct($idpais = null, $nome = null, $geom = null, $cod_ibge = null)
    {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = "{$this->_schema}pais";

        $this->_campos_lista = $this->_todos_campos = 'idpais, nome, geom, cod_ibge';

        if (is_numeric($idpais)) {
            $this->idpais = $idpais;
        }
        if (is_numeric($cod_ibge)) {
            $this->cod_ibge = $cod_ibge;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_string($geom)) {
            $this->geom = $geom;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->nome)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }
            if (is_string($this->geom)) {
                $campos .= "{$gruda}geom";
                $valores .= "{$gruda}'{$this->geom}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $campos .= "{$gruda}cod_ibge";
                $valores .= "{$gruda}'{$this->cod_ibge}'";
                $gruda = ', ';
            }

            $idpais = $db->campoUnico("SELECT COALESCE( MAX(idpais), 0 ) + 1 FROM {$this->_tabela}");

            $db->Consulta("INSERT INTO {$this->_tabela} ( idpais, $campos ) VALUES( $idpais, $valores )");

            return $idpais;
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
        if (is_numeric($this->idpais)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }
            if (is_string($this->geom)) {
                $set .= "{$gruda}geom = '{$this->geom}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $set .= "{$gruda}cod_ibge = '{$this->cod_ibge}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idpais = '{$this->idpais}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @param string str_nome
     * @param string str_geom
     *
     * @return array
     */
    public function lista($int_idpais = null, $str_nome = null, $str_geom = null)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_idpais)) {
            $filtros .= "{$whereAnd} idpais = '{$int_idpais}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $filtros .= "{$whereAnd} geom LIKE '%{$str_geom}%'";
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
        if (is_numeric($this->idpais)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idpais = '{$this->idpais}'");
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
        if (is_numeric($this->idpais)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE idpais = '{$this->idpais}'");
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
        if (is_numeric($this->idpais)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE idpais = '{$this->idpais}'");

            return true;
        }

        return false;
    }
}
