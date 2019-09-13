<?php

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

class clsCepLogradouro
{
    public $cep;
    public $idlog;
    public $nroini;
    public $nrofin;
    public $tabela;
    public $schema;

    public function __construct($cep = false, $idlog = false, $nroini = false, $nrofin = false)
    {
        $objLogradouro = new clsLogradouro($idlog);

        if ($objLogradouro->detalhe()) {
            $this->idlog = $idlog;
        }

        $this->cep = $cep;
        $this->nroini = $nroini;
        $this->nrofin = $nrofin;

        $this->tabela = 'cep_logradouro';
        $this->schema = 'urbano';
    }

    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    public function cadastra()
    {
        $db = new clsBanco();

        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $campos = '';
            $values = '';
            if (is_numeric($this->nroini)) {
                $campos .= ', nroini';
                $values .= ", '{$this->nroini}'";
            }
            if (is_numeric($this->nrofin)) {
                $campos .= ', nrofin';
                $valores .= ", '$this->nrofin' ";
            }

            $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (cep,  idlog, origem_gravacao, data_cad, operacao $campos) VALUES ( '{$this->cep}', '{$this->idlog}','U', NOW(), 'I' $values )");
        }

        return false;
    }

    /**
     * Edita o registro atual
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $gruda = '';
            if ($this->nroini) {
                $set .= " nroini = '{$this->nroini}'";
                $gruda = ', ';
            } else {
                $set .= ' nroini = NULL';
                $gruda = ', ';
            }

            if ($this->nrofin) {
                $set .= "$gruda nrofin = '{$this->nrofin}'";
            } else {
                $set .= ' nrofin = NULL';
                $gruda = ', ';
            }

            if ($set) {
                $set = "SET {$set}";
                $db = new clsBanco();
                $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE cep = '$this->cep' AND idlog = '$this->idlog");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->schema}.{$this->tabela} WHERE cep = '{$this->cep}' AND idlog = '{$this->idlog}' ");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Remove o registro atual
     *
     * @return bool
     */
    public function exclui()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog)) {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE cep = {$this->cep} AND idlog = {$this->idlog}");

            return true;
        }

        return false;
    }

    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    public function lista($int_cep = false, $int_idlog = false, $int_nroini = false, $int_nrofin = false, $str_ordenacao = 'cep', $int_limite_ini = 0, $int_limite_qtd = 20)
    {
        $whereAnd = 'WHERE ';
        if (is_numeric($int_cep)) {
            $where .= "{$whereAnd}cep = '$int_cep'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_idlog)) {
            $where .= "{$whereAnd}idlog = '$int_idlog'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nroini)) {
            $where .= "{$whereAnd}nroini = '$int_nroini'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_nrofin)) {
            $where .= "{$whereAnd}nrofin =  '$int_nrofin'";
        }

        $orderBy = '';
        if (is_string($str_ordenacao)) {
            $orderBy = "ORDER BY $str_ordenacao";
        }
        $limit = '';
        if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
            $limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
        }

        $db = new clsBanco();
        $db->Consulta("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
        $db->ProximoRegistro();
        $total = $db->Campo('total');
        $db->Consulta("SELECT cep, idlog, nroini, nrofin FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");

        $resultado = [];

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $tupla['idlog'] = new clsLogradouro($tupla['idlog']);
            $tupla['total'] = $total;
            $resultado[] = $tupla;
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    public function detalhe()
    {
        if ($this->cep && $this->idlog) {
            $db = new clsBanco();
            $db->Consulta("SELECT cep, idlog, nroini, nrofin FROM {$this->schema}.{$this->tabela} WHERE cep = {$this->cep} AND idlog = {$this->idlog}");
            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['idlog'] = new clsLogradouro($tupla['idlog']);

                return $tupla;
            }
        }

        return false;
    }
}
