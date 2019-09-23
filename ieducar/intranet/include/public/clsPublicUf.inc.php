<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicUf extends Model
{
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;
    public $cod_ibge;

    /**
     * Construtor (PHP 4)
     *
     * @param string sigla_uf
     * @param string nome
     * @param string geom
     * @param integer idpais
     *
     * @return object
     */
    public function __construct($sigla_uf = null, $nome = null, $geom = null, $idpais = null, $cod_ibge = null)
    {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = "{$this->_schema}uf";

        $this->_campos_lista = $this->_todos_campos = 'uf.sigla_uf, uf.nome, uf.geom, uf.idpais, uf.cod_ibge ';

        if (is_numeric($idpais)) {
                    $this->idpais = $idpais;
        }

        if (is_string($sigla_uf)) {
            $this->sigla_uf = $sigla_uf;
        }
        if (is_string($nome)) {
            $this->nome = $nome;
        }
        if (is_string($geom)) {
            $this->geom = $geom;
        }
        if (is_numeric($cod_ibge)) {
            $this->cod_ibge = $cod_ibge;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->sigla_uf) && is_string($this->nome) && is_numeric($this->idpais)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->sigla_uf)) {
                $campos .= "{$gruda}sigla_uf";
                $valores .= "{$gruda}'{$this->sigla_uf}'";
                $gruda = ', ';
            }
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
            if (is_numeric($this->idpais)) {
                $campos .= "{$gruda}idpais";
                $valores .= "{$gruda}'{$this->idpais}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $campos .= "{$gruda}cod_ibge";
                $valores .= "{$gruda}'{$this->cod_ibge}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $this->sigla_uf;
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
        if (is_string($this->sigla_uf)) {
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
            if (is_numeric($this->idpais)) {
                $set .= "{$gruda}idpais = '{$this->idpais}'";
                $gruda = ', ';
            }
            if (is_numeric($this->cod_ibge)) {
                $set .= "{$gruda}cod_ibge = '{$this->cod_ibge}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE sigla_uf = '{$this->sigla_uf}'");

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
     * @param integer int_idpais
     *
     * @return array
     */
    public function lista($str_nome = null, $str_geom = null, $int_idpais = null, $str_sigla_uf = null)
    {
        $sql = "SELECT {$this->_campos_lista}, p.nome AS nm_pais FROM {$this->_tabela} uf, public.pais p ";
        $whereAnd = ' AND ';

        $filtros = ' WHERE uf.idpais = p.idpais';

        if (is_string($str_sigla_uf)) {
            $filtros .= "{$whereAnd} uf.sigla_uf LIKE '%{$str_sigla_uf}%'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} uf.nome LIKE E'%" . addslashes($str_nome) . '%\'';
            $whereAnd = ' AND ';
        }
        if (is_string($str_geom)) {
            $filtros .= "{$whereAnd} uf.geom LIKE '%{$str_geom}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idpais)) {
            $filtros .= "{$whereAnd} uf.idpais = '{$int_idpais}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} uf, public.pais p {$filtros}");

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
        if (is_string($this->sigla_uf)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} uf WHERE uf.sigla_uf = '{$this->sigla_uf}'");
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
        if (is_string($this->sigla_uf)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE sigla_uf = '{$this->sigla_uf}'");
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
        if (is_string($this->sigla_uf)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE sigla_uf = '{$this->sigla_uf}'");

            return true;
        }

        return false;
    }

    public function verificaDuplicidade()
    {
        $db = new clsBanco();
        $sql = "SELECT sigla_uf
              FROM public.uf
              WHERE sigla_uf = '{$this->sigla_uf}'";
        $db->Consulta($sql);

        return $db->ProximoRegistro();
    }
}
